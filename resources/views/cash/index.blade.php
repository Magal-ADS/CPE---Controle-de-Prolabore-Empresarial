@php($title = 'Caixa | CPE')
@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Caixa</p>
                <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Saldo atual e retiradas</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Visao direta do valor disponivel em caixa e do historico de saidas registradas.</p>
            </div>

            <form method="GET" class="grid gap-3 rounded-3xl border border-slate-200 p-4 sm:grid-cols-3 dark:border-slate-800">
                <div>
                    <label for="start_date" class="form-label">Inicio</label>
                    <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}" class="form-input">
                </div>
                <div>
                    <label for="end_date" class="form-label">Fim</label>
                    <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}" class="form-input">
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">
                    Filtrar retiradas
                </button>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="stat-card">
                <p class="stat-label">Valor atual do caixa</p>
                <p class="stat-value">R$ {{ number_format($currentBalance, 2, ',', '.') }}</p>
                <p class="stat-footnote">Saldo consolidado entre entradas e saidas</p>
            </article>
            <article class="stat-card">
                <p class="stat-label">Retiradas filtradas</p>
                <p class="stat-value text-rose-600 dark:text-rose-400">R$ {{ number_format($filteredWithdrawalsTotal, 2, ',', '.') }}</p>
                <p class="stat-footnote">Total visivel na listagem abaixo</p>
            </article>
            <article class="stat-card">
                <p class="stat-label">Total historico de retiradas</p>
                <p class="stat-value text-slate-900 dark:text-slate-100">R$ {{ number_format($totalWithdrawals, 2, ',', '.') }}</p>
                <p class="stat-footnote">Historico total de saidas cadastradas</p>
            </article>
        </div>

        <section class="rounded-[1.75rem] border border-slate-200 p-4 dark:border-slate-800">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Historico das retiradas</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Cada saida cadastrada aparece aqui com data, responsavel e descricao.</p>
                </div>
                <a href="{{ route('transactions.create') }}" class="rounded-2xl bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">
                    Nova retirada
                </a>
            </div>

            <div class="grid gap-4">
                @forelse ($withdrawals as $withdrawal)
                    <article class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                        Retirada
                                    </span>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $withdrawal->transaction_date->format('d/m/Y') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Lancado por {{ $withdrawal->user?->name }}</p>
                                </div>
                                <h3 class="mt-3 text-lg font-semibold">{{ $withdrawal->description }}</h3>
                                <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400">R$ {{ number_format((float) $withdrawal->amount, 2, ',', '.') }}</p>

                                @if ($withdrawal->attachment_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($withdrawal->attachment_path) }}" target="_blank" class="mt-3 inline-flex text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">
                                        Ver comprovante
                                    </a>
                                @endif
                            </div>

                            <a href="{{ route('transactions.edit', $withdrawal) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
                                Editar movimentacao
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[1.75rem] border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        Nenhuma retirada registrada para o filtro atual.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $withdrawals->links() }}
            </div>
        </section>
    </div>
@endsection
