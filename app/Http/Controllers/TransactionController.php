<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = Transaction::query()
            ->with('user')
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('transaction_date', '>=', $request->string('start_date')))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('transaction_date', '<=', $request->string('end_date')))
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        return view('transactions.create', [
            'transaction' => new Transaction([
                'transaction_date' => now()->toDateString(),
                'type' => 'income',
            ]),
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('transactions', 'public');
        }

        Transaction::create($data);

        return redirect()->route('transactions.index')
            ->with('status', 'Movimentacao cadastrada com sucesso.');
    }

    public function edit(Transaction $transaction): View
    {
        return view('transactions.edit', compact('transaction'));
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')->store('transactions', 'public');
        }

        $transaction->update($data);

        return redirect()->route('transactions.index')
            ->with('status', 'Movimentacao atualizada com sucesso.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->attachment_path) {
            Storage::disk('public')->delete($transaction->attachment_path);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('status', 'Movimentacao removida com sucesso.');
    }
}
