<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $startDate = rescue(
            fn () => Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay(),
            now()->startOfMonth()->startOfDay(),
            false
        );
        $endDate = rescue(
            fn () => Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()))->endOfDay(),
            now()->endOfMonth()->endOfDay(),
            false
        );

        if ($endDate->lt($startDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $transactions = DB::table('transactions')
            ->select('type', 'amount', 'transaction_date')
            ->whereNotNull('transaction_date')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('transaction_date')
            ->get();

        $incomeTotal = (float) $transactions
            ->where('type', 'income')
            ->sum(fn ($transaction) => (float) $transaction->amount);
        $expenseTotal = (float) $transactions
            ->where('type', 'expense')
            ->sum(fn ($transaction) => (float) $transaction->amount);
        $balance = (float) DB::table('transactions')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END), 0) as balance")
            ->value('balance');

        $period = collect();
        $cursor = $startDate->copy();

        while ($cursor <= $endDate) {
            $label = $cursor->format('d/m');
            $period->put($label, ['income' => 0, 'expense' => 0]);
            $cursor->addDay();
        }

        foreach ($transactions as $transaction) {
            $label = rescue(function () use ($transaction) {
                return Carbon::parse((string) $transaction->transaction_date)->format('d/m');
            }, null, false);

            if ($label === null || ! isset($period[$label], $period[$label][$transaction->type])) {
                continue;
            }

            $totals = $period->get($label);
            $totals[$transaction->type] += (float) $transaction->amount;
            $period->put($label, $totals);
        }

        $recentTransactions = DB::table('transactions')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->select([
                'transactions.type',
                'transactions.amount',
                'transactions.description',
                'transactions.transaction_date',
                'users.name as user_name',
            ])
            ->whereNotNull('transaction_date')
            ->latest('transaction_date')
            ->latest('transactions.id')
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                $transaction->formatted_transaction_date = rescue(
                    fn () => Carbon::parse((string) $transaction->transaction_date)->format('d/m/Y'),
                    'Data indisponivel',
                    false
                );

                return $transaction;
            });

        return view('dashboard.index', [
            'balance' => $balance,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'chartLabels' => $period->keys()->values(),
            'incomeSeries' => $period->pluck('income')->values(),
            'expenseSeries' => $period->pluck('expense')->values(),
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
