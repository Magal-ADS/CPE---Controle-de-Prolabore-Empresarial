<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashController extends Controller
{
    public function __invoke(Request $request): View
    {
        $withdrawalsQuery = Transaction::query()
            ->where('type', 'expense')
            ->when(
                $request->filled('start_date'),
                fn ($query) => $query->whereDate('transaction_date', '>=', $request->string('start_date'))
            )
            ->when(
                $request->filled('end_date'),
                fn ($query) => $query->whereDate('transaction_date', '<=', $request->string('end_date'))
            );

        $withdrawals = (clone $withdrawalsQuery)
            ->withoutAttachmentContent()
            ->with('user')
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        $currentBalance = (float) Transaction::query()
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END), 0) as balance")
            ->value('balance');

        $filteredWithdrawalsTotal = (float) (clone $withdrawalsQuery)
            ->sum('amount');

        $totalWithdrawals = (float) Transaction::query()
            ->where('type', 'expense')
            ->sum('amount');

        return view('cash.index', [
            'currentBalance' => $currentBalance,
            'filteredWithdrawalsTotal' => $filteredWithdrawalsTotal,
            'totalWithdrawals' => $totalWithdrawals,
            'withdrawals' => $withdrawals,
        ]);
    }
}
