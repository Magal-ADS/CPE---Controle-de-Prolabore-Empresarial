<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()))->endOfDay();

        $transactions = Transaction::query()
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
            $label = $transaction->transaction_date->format('d/m');
            $period[$label][$transaction->type] += (float) $transaction->amount;
        }

        return view('dashboard.index', [
            'balance' => $balance,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'chartLabels' => $period->keys()->values(),
            'incomeSeries' => $period->pluck('income')->values(),
            'expenseSeries' => $period->pluck('expense')->values(),
            'recentTransactions' => Transaction::query()->with('user')->latest('transaction_date')->limit(5)->get(),
        ]);
    }
}
