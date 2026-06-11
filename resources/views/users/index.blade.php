@php($title = 'Usuarios | CPE')
@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Acesso</p>
                <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Gestao de usuarios</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Todos os socios visualizam o mesmo conjunto de movimentacoes.</p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-amber-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">
                Novo usuario
            </a>
        </div>

        <div class="grid gap-4">
            @foreach ($users as $user)
                <article class="rounded-[1.75rem] border border-slate-200 p-4 dark:border-slate-800">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
                                <span @class([
                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => $user->is_active,
                                    'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' => ! $user->is_active,
                                ])>
                                    {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
                                Editar
                            </a>
                            @if ($user->is_active)
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Deseja inativar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-rose-500">
                                        Inativar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{ $users->links() }}
    </div>
@endsection
