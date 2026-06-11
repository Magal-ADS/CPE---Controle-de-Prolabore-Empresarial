@php($title = 'Novo usuario | CPE')
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Cadastro</p>
        <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Novo usuario</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Cadastre socios ou responsaveis com visibilidade total do portal.</p>

        <form method="POST" action="{{ route('users.store') }}" class="mt-6 rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
            @include('users._form', ['submitLabel' => 'Salvar usuario'])
        </form>
    </div>
@endsection
