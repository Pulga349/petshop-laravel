<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Vantablack Monochrome — compact no-build fallback */
                *, *::before, *::after { box-sizing: border-box; border-radius: 0; }
                html { background: #000; }
                body {
                    margin: 0;
                    min-height: 100vh;
                    background: #000;
                    color: #fff;
                    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
                    -webkit-font-smoothing: antialiased;
                }
                a { color: #8d8d8d; text-decoration: underline; text-underline-offset: 4px; }
                a:hover { color: #fff; }
                a:focus-visible { outline: 2px solid #8d8d8d; outline-offset: 2px; }
                .surface { background: #0d0d0d; border: 1px solid #262626; padding: 40px 32px; }
                .btn-primary, .btn-secondary {
                    display: inline-flex; align-items: center; justify-content: center;
                    min-height: 44px; padding: 0 20px; font-size: 14px; font-weight: 600;
                    text-decoration: none;
                }
                .btn-primary { background: #fff; color: #000; border: 1px solid #fff; }
                .btn-primary:hover { background: #a3a3a3; color: #000; }
                .btn-secondary { background: #161616; color: #fff; border: 1px solid #262626; }
                .btn-secondary:hover { background: #222; color: #fff; }
                .landing-header, .landing-main {
                    width: 100%; max-width: 896px; margin: 0 auto; padding-left: 24px; padding-right: 24px;
                }
                .landing-header { display: flex; justify-content: flex-end; padding-top: 32px; padding-bottom: 24px; }
                .landing-nav { display: flex; gap: 12px; }
                .landing-main { display: grid; grid-template-columns: 1fr; gap: 24px; padding-bottom: 48px; }
                @media (min-width: 1024px) { .landing-main { grid-template-columns: 1fr 1fr; } }
                .landing-title { margin: 0; font-size: 28px; line-height: 1.2; font-weight: 700; letter-spacing: -0.02em; }
                .landing-lead { margin: 8px 0 0; font-size: 14px; color: #a3a3a3; }
                .landing-list { margin: 24px 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 16px; }
                .landing-list li { display: flex; align-items: flex-start; gap: 12px; font-size: 14px; }
                .landing-marker { width: 8px; height: 8px; margin-top: 6px; flex-shrink: 0; background: #222; border: 1px solid #262626; }
                .landing-actions { margin: 0; padding: 0; list-style: none; display: flex; gap: 12px; }
                .landing-version { margin: 32px 0 0; font-size: 12px; color: #a3a3a3; }
                .landing-brand { display: flex; align-items: center; justify-content: center; min-height: 320px; text-align: center; }
            </style>
        @endif
    </head>
    <body>
        <header class="landing-header mx-auto flex w-full max-w-4xl items-center justify-end gap-4 px-6 pt-8 pb-6">
            @if (Route::has('login'))
                <nav class="landing-nav flex items-center gap-3">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="btn-primary"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="btn-secondary"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="btn-primary">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="landing-main mx-auto grid w-full max-w-4xl grid-cols-1 gap-6 px-6 pb-12 lg:grid-cols-2">
            <section class="surface p-8 lg:p-10">
                <h1 class="landing-title text-2xl font-bold tracking-tight text-white sm:text-3xl">Let's get started</h1>
                <p class="landing-lead mt-2 text-sm text-neutral-400">With so many options available to you,<br /> we suggest you start with the following:</p>
                <ul class="landing-list mt-6 mb-6 flex flex-col gap-4">
                    <li class="flex items-start gap-3 text-sm text-white">
                        <span class="landing-marker mt-1.5 h-2 w-2 shrink-0 border border-line bg-surface-hover" aria-hidden="true"></span>
                        <span>
                            Read the
                            <a href="https://laravel.com/docs" target="_blank" class="font-medium text-accent underline underline-offset-4 hover:text-white">Documentation</a>
                        </span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-white">
                        <span class="landing-marker mt-1.5 h-2 w-2 shrink-0 border border-line bg-surface-hover" aria-hidden="true"></span>
                        <span>
                            Watch video tutorials at
                            <a href="https://laracasts.com" target="_blank" class="font-medium text-accent underline underline-offset-4 hover:text-white">Laracasts</a>
                        </span>
                    </li>
                </ul>
                <ul class="landing-actions flex gap-3">
                    <li>
                        <a href="https://cloud.laravel.com" target="_blank" class="btn-primary">
                            Deploy now
                        </a>
                    </li>
                </ul>

                <p class="landing-version mt-8 text-xs text-neutral-400">
                    v{{ app()->version() }}
                    <a href="https://github.com/laravel/framework/blob/13.x/CHANGELOG.md" target="_blank" class="text-accent underline underline-offset-4 hover:text-white">
                        View changelog
                    </a>
                </p>
            </section>

            <section class="surface landing-brand p-8 text-center lg:p-10">
                <div>
                    <p class="landing-title text-3xl font-bold tracking-tight text-white">{{ config('app.name', 'Laravel') }}</p>
                </div>
            </section>
        </main>
    </body>
</html>
