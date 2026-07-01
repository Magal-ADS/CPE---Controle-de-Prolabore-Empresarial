<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()))->endOfDay();

        $transactions = Transaction::query()
            ->whereNotNull('transaction_date')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('transaction_date')
            ->get();

        $incomeTotal = (float) $transactions->where('type', 'income')->sum('amount');
        $expenseTotal = (float) $transactions->where('type', 'expense')->sum('amount');
        $balance = (float) Transaction::query()
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
                $date = $transaction->transaction_date;

                if ($date instanceof CarbonInterface) {
                    return $date->format('d/m');
                }

                return Carbon::parse((string) $date)->format('d/m');
            }, null, false);

            if ($label === null || ! isset($period[$label], $period[$label][$transaction->type])) {
                continue;
            }

            $period[$label][$transaction->type] += (float) $transaction->amount;
        }

        $recentTransactions = Transaction::query()
            ->with('user')
            ->whereNotNull('transaction_date')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get()
            ->each(function (Transaction $transaction): void {
                $transaction->formatted_transaction_date = rescue(function () use ($transaction) {
                    $date = $transaction->transaction_date;

                    if ($date instanceof CarbonInterface) {
                        return $date->format('d/m/Y');
                    }

                    return Carbon::parse((string) $date)->format('d/m/Y');
                }, null, false);
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
