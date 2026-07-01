@php($title = 'Dashboard | CPE')
@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Visao geral</p>
                <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Painel financeiro</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Entradas e saidas centralizadas para todos os socios do portal.</p>
            </div>

            <form method="GET" class="grid gap-3 rounded-3xl border border-slate-200 p-4 sm:grid-cols-3 dark:border-slate-800">
                <div>
                    <label for="start_date" class="form-label">Inicio</label>
                    <input id="start_date" name="start_date" type="date" value="{{ $startDate }}" class="form-input">
                </div>
                <div>
                    <label for="end_date" class="form-label">Fim</label>
                    <input id="end_date" name="end_date" type="date" value="{{ $endDate }}" class="form-input">
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">Aplicar filtro</button>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="stat-card">
                <p class="stat-label">Saldo atual</p>
                <p class="stat-value">R$ {{ number_format($balance, 2, ',', '.') }}</p>
                <p class="stat-footnote">Resultado acumulado da empresa</p>
            </article>
            <article class="stat-card">
                <p class="stat-label">Total de entradas</p>
                <p class="stat-value text-emerald-600 dark:text-emerald-400">R$ {{ number_format($incomeTotal, 2, ',', '.') }}</p>
                <p class="stat-footnote">Periodo selecionado</p>
            </article>
            <article class="stat-card">
                <p class="stat-label">Total de saidas</p>
                <p class="stat-value text-rose-600 dark:text-rose-400">R$ {{ number_format($expenseTotal, 2, ',', '.') }}</p>
                <p class="stat-footnote">Periodo selecionado</p>
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.45fr_0.95fr]">
            <section class="rounded-[1.75rem] border border-slate-200 p-4 dark:border-slate-800">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Entradas vs Saidas</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Comparativo diario do periodo.</p>
                    </div>
                    <a href="{{ route('transactions.create') }}" class="rounded-2xl bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">Nova movimentacao</a>
                </div>
                <div class="h-80">
                    <canvas id="financialChart"></canvas>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-slate-200 p-4 dark:border-slate-800">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">Ultimas movimentacoes</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Linha do tempo recente do caixa.</p>
                </div>

                <div class="grid gap-3">
                    @forelse ($recentTransactions as $transaction)
                        <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold">{{ $transaction->description }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        {{ $transaction->user?->name ?? 'Usuario indisponivel' }} - {{ $transaction->formatted_transaction_date ?? 'Data indisponivel' }}
                                    </p>
                                </div>
                                <span @class([
                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $transaction->type === 'income',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' => $transaction->type === 'expense',
                                ])>
                                    {{ $transaction->type === 'income' ? 'Entrada' : 'Saida' }}
                                </span>
                            </div>
                            <p class="mt-3 text-lg font-bold">R$ {{ number_format((float) $transaction->amount, 2, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            Nenhuma movimentacao encontrada para exibir no painel.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartElement = document.getElementById('financialChart');
        if (chartElement) {
            new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Entradas',
                            data: @json($incomeSeries),
                            backgroundColor: '#10b981',
                            borderRadius: 10,
                        },
                        {
                            label: 'Saidas',
                            data: @json($expenseSeries),
                            backgroundColor: '#f43f5e',
                            borderRadius: 10,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
@endpush
