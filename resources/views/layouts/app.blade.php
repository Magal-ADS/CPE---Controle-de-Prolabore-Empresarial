<!DOCTYPE html>
<html lang="pt-BR" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CPE - Controle de Prolabore Empresarial' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script>
        (() => {
            const storedTheme = localStorage.getItem('cpe-theme');
            const isDark = storedTheme ? storedTheme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.dataset.theme = isDark ? 'dark' : 'light';
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[var(--app-bg)] text-[var(--app-text)] antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,#d7f0df_0%,transparent_35%),radial-gradient(circle_at_bottom,#f1d8bf_0%,transparent_30%)] opacity-80 dark:bg-[radial-gradient(circle_at_top,#1a3a32_0%,transparent_30%),radial-gradient(circle_at_bottom,#3d2d21_0%,transparent_25%)]"></div>
        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-4 sm:px-6 lg:flex-row lg:px-8">
            @auth
                <div id="mobile-menu-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

                <header class="mb-4 flex items-center justify-between rounded-3xl border border-white/70 bg-white/80 px-4 py-3 shadow-[0_20px_80px_rgba(15,23,42,0.08)] backdrop-blur dark:border-white/10 dark:bg-slate-950/75 dark:shadow-none lg:hidden">
                    <div>
                        <p class="font-display text-lg font-bold tracking-tight">CPE</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Controle de Prolabore Empresarial</p>
                    </div>

                    <button type="button" id="mobile-menu-toggle" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white" aria-label="Abrir menu">
                        <span class="flex flex-col gap-1.5">
                            <span class="h-0.5 w-5 rounded-full bg-current"></span>
                            <span class="h-0.5 w-5 rounded-full bg-current"></span>
                            <span class="h-0.5 w-5 rounded-full bg-current"></span>
                        </span>
                    </button>
                </header>

                <aside id="mobile-drawer" class="fixed inset-y-4 left-4 z-50 flex w-[min(20rem,calc(100vw-2rem))] -translate-x-[120%] flex-col rounded-3xl border border-white/70 bg-white/95 p-5 shadow-[0_20px_80px_rgba(15,23,42,0.2)] backdrop-blur transition-transform duration-200 dark:border-white/10 dark:bg-slate-950/95 dark:shadow-none lg:hidden">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-display text-xl font-bold tracking-tight">CPE</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Controle de Prolabore Empresarial</p>
                        </div>
                        <button type="button" id="mobile-menu-close" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white" aria-label="Fechar menu">
                            <span class="text-sm font-semibold">X</span>
                        </button>
                    </div>

                    <nav class="mt-6 grid gap-2">
                        <a href="{{ route('dashboard') }}" data-mobile-nav-link @class(['nav-link', 'nav-link-active' => request()->routeIs('dashboard')])>Dashboard</a>
                        <a href="{{ route('cash.index') }}" data-mobile-nav-link @class(['nav-link', 'nav-link-active' => request()->routeIs('cash.*')])>Caixa</a>
                        <a href="{{ route('transactions.index') }}" data-mobile-nav-link @class(['nav-link', 'nav-link-active' => request()->routeIs('transactions.*')])>Movimentacoes</a>
                        <a href="{{ route('users.index') }}" data-mobile-nav-link @class(['nav-link', 'nav-link-active' => request()->routeIs('users.*')])>Usuarios</a>
                        <a href="{{ route('profile.edit') }}" data-mobile-nav-link @class(['nav-link', 'nav-link-active' => request()->routeIs('profile.*')])>Meu perfil</a>
                    </nav>

                    <div class="mt-auto pt-6">
                        <div class="rounded-2xl bg-slate-950 px-4 py-4 text-white dark:bg-white dark:text-slate-900">
                            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-white/70 dark:text-slate-500">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="mt-4 flex gap-3">
                            <button type="button" data-theme-toggle class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
                                Alternar tema
                            </button>
                            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-amber-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>

                <aside class="mb-4 hidden w-full flex-col rounded-3xl border border-white/70 bg-white/80 p-5 shadow-[0_20px_80px_rgba(15,23,42,0.08)] backdrop-blur lg:mb-0 lg:mr-6 lg:flex lg:min-h-[calc(100vh-2rem)] lg:w-72 dark:border-white/10 dark:bg-slate-950/75 dark:shadow-none">
                    <div>
                        <p class="font-display text-xl font-bold tracking-tight">CPE</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Controle de Prolabore Empresarial</p>
                    </div>

                    <nav class="mt-6 grid gap-2">
                        <a href="{{ route('dashboard') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('dashboard')])>Dashboard</a>
                        <a href="{{ route('cash.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('cash.*')])>Caixa</a>
                        <a href="{{ route('transactions.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('transactions.*')])>Movimentacoes</a>
                        <a href="{{ route('users.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('users.*')])>Usuarios</a>
                        <a href="{{ route('profile.edit') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('profile.*')])>Meu perfil</a>
                    </nav>

                    <div class="mt-auto pt-6">
                        <div class="rounded-2xl bg-slate-950 px-4 py-4 text-white dark:bg-white dark:text-slate-900">
                            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-white/70 dark:text-slate-500">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="mt-4 flex gap-3">
                            <button type="button" data-theme-toggle class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
                                Alternar tema
                            </button>
                            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-amber-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-400">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </aside>
            @endauth

            <main class="flex-1">
                <div class="rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-[0_20px_80px_rgba(15,23,42,0.08)] backdrop-blur sm:p-6 dark:border-white/10 dark:bg-slate-950/75 dark:shadow-none">
                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-theme-toggle]').forEach((themeToggle) => {
            themeToggle.addEventListener('click', () => {
                const root = document.documentElement;
                const dark = root.classList.toggle('dark');
                root.dataset.theme = dark ? 'dark' : 'light';
                localStorage.setItem('cpe-theme', dark ? 'dark' : 'light');
            });
        });

        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileDrawer = document.getElementById('mobile-drawer');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileNavLinks = document.querySelectorAll('[data-mobile-nav-link]');

        const openMobileMenu = () => {
            if (!mobileDrawer || !mobileMenuOverlay) {
                return;
            }

            mobileDrawer.classList.remove('-translate-x-[120%]');
            mobileMenuOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeMobileMenu = () => {
            if (!mobileDrawer || !mobileMenuOverlay) {
                return;
            }

            mobileDrawer.classList.add('-translate-x-[120%]');
            mobileMenuOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        mobileMenuToggle?.addEventListener('click', openMobileMenu);
        mobileMenuClose?.addEventListener('click', closeMobileMenu);
        mobileMenuOverlay?.addEventListener('click', closeMobileMenu);
        mobileNavLinks.forEach((link) => link.addEventListener('click', closeMobileMenu));
    </script>
    @stack('scripts')
</body>
</html>
