@php($title = 'Nova movimentacao | CPE')
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Cadastro</p>
        <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Nova movimentacao</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Registre entradas e saidas com contexto e comprovantes.</p>

        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data" class="mt-6 rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
            @include('transactions._form', ['submitLabel' => 'Salvar movimentacao'])
        </form>
    </div>
@endsection
