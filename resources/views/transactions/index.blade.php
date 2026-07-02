@php($title = 'Movimentacoes | CPE')
@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Caixa</p>
                <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Gestao de movimentacoes</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">CRUD completo de entradas e saidas com comprovantes anexados.</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">
                Nova movimentacao
            </a>
        </div>

        <form method="GET" class="grid gap-4 rounded-[1.75rem] border border-slate-200 p-4 md:grid-cols-4 dark:border-slate-800">
            <div>
                <label for="type" class="form-label">Tipo</label>
                <select id="type" name="type" class="form-input">
                    <option value="">Todos</option>
                    <option value="income" @selected(request('type') === 'income')>Entrada</option>
                    <option value="expense" @selected(request('type') === 'expense')>Saida</option>
                </select>
            </div>
            <div>
                <label for="start_date" class="form-label">Inicio</label>
                <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}" class="form-input">
            </div>
            <div>
                <label for="end_date" class="form-label">Fim</label>
                <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}" class="form-input">
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">
                    Filtrar
                </button>
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
                    Limpar
                </a>
            </div>
        </form>

        <div class="grid gap-4">
            @forelse ($transactions as $transaction)
                <article class="rounded-[1.75rem] border border-slate-200 p-4 dark:border-slate-800">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <span @class([
                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $transaction->type === 'income',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' => $transaction->type === 'expense',
                                ])>
                                    {{ $transaction->type === 'income' ? 'Entrada' : 'Saida' }}
                                </span>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Lancado por {{ $transaction->user?->name }}</p>
                            </div>
                            <h2 class="text-lg font-semibold">{{ $transaction->description }}</h2>
                            <p class="text-2xl font-bold">R$ {{ number_format((float) $transaction->amount, 2, ',', '.') }}</p>
                            @if ($transaction->hasAttachment())
                                <a href="{{ route('transactions.attachment', $transaction) }}" target="_blank" class="inline-flex text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">
                                    Abrir comprovante
                                </a>
                            @endif
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('transactions.edit', $transaction) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Deseja remover esta movimentacao?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-rose-500">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                    Nenhuma movimentacao cadastrada ate o momento.
                </div>
            @endforelse
        </div>

        {{ $transactions->links() }}
    </div>
@endsection
