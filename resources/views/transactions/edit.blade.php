@php($title = 'Editar movimentacao | CPE')
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Edicao</p>
        <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Editar movimentacao</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Atualize dados financeiros e gerencie o comprovante vinculado.</p>

        <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data" class="mt-6 rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
            @method('PUT')
            @include('transactions._form', ['submitLabel' => 'Atualizar movimentacao'])
        </form>
    </div>
@endsection
