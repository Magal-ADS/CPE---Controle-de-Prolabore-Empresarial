@php($title = 'Meu perfil | CPE')
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-400">Perfil</p>
        <h1 class="mt-2 font-display text-3xl font-bold tracking-tight">Atualizar meus dados</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Edite nome, e-mail e senha do usuario autenticado.</p>

        <form method="POST" action="{{ route('profile.update') }}" class="mt-6 rounded-[1.75rem] border border-slate-200 p-5 dark:border-slate-800">
            @csrf
            @method('PUT')

            <div class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label for="name" class="form-label">Nome</label>
                    <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" class="form-input">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="form-input">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label">Nova senha</label>
                    <input id="password" name="password" type="password" class="form-input">
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirmar nova senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-input">
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">
                    Salvar alteracoes
                </button>
            </div>
        </form>
    </div>
@endsection
