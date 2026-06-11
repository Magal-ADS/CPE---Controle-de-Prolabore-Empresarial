@csrf

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <label for="name" class="form-label">Nome</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="form-input">
        @error('name')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="form-label">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-input">
        @error('email')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="form-label">Senha</label>
        <input id="password" name="password" type="password" class="form-input">
        @error('password')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="form-label">Confirmar senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-input">
    </div>

    <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-200 lg:col-span-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
        Usuario ativo
    </label>
</div>

<div class="mt-6 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
        Cancelar
    </a>
</div>
