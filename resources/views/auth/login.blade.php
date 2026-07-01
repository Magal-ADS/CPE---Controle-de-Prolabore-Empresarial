<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso | CPE</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--app-bg)] text-[var(--app-text)] antialiased">
    <div class="relative isolate flex min-h-screen items-center justify-center overflow-hidden px-4 py-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,#d7f0df_0%,transparent_35%),radial-gradient(circle_at_bottom,#f1d8bf_0%,transparent_30%)]"></div>
        <div class="relative grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/60 bg-white/85 shadow-[0_20px_80px_rgba(15,23,42,0.12)] backdrop-blur lg:grid-cols-[1.15fr_0.85fr] dark:border-white/10 dark:bg-slate-950/85 dark:shadow-none">
            <section class="hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="font-display text-3xl font-bold">CPE</p>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-slate-300">
                        Transparencia total das entradas e saidas para os socios, com visao clara do caixa e comprovantes centralizados.
                    </p>
                </div>
                <div class="grid gap-4">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm font-semibold">Base inicial pronta para operacao</p>
                        <p class="mt-2 text-sm text-slate-300">Dashboard, movimentacoes, usuarios, perfil e suporte a anexos.</p>
                    </div>
                </div>
            </section>

            <section class="p-6 sm:p-10">
                <div class="mb-8">
                    <p class="font-display text-3xl font-bold tracking-tight">Entrar no portal</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Acesse o ambiente financeiro com suas credenciais autorizadas.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="grid gap-5">
                    @csrf

                    <div>
                        <label for="email" class="form-label">E-mail</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="form-input">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="form-label">Senha</label>
                        <input id="password" name="password" type="password" required class="form-input">
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="inline-flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Manter sessao ativa
                    </label>

                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-emerald-500 dark:text-slate-950 dark:hover:bg-emerald-400">
                        Acessar sistema
                    </button>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
