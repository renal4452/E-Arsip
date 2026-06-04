<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Internal Inspektorat</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Supaya x-cloak bekerja seperti layout utama --}}
    <style>
        [x-cloak] { display: none !important; }
        /* Memastikan body mengambil warna dasar dari sistem token secara langsung */
        body {
            background-color: var(--bg-base);
            color: var(--text-1);
        }
    </style>

    {{-- Alpine untuk x-data/x-show pada tombol mata password --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="antialiased min-h-screen flex items-center justify-center selection:bg-[var(--accent)] selection:text-white">
    <div class="w-full max-w-md px-4">
        <div class="card-base p-8">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-4 flex items-center justify-center w-12 h-12 rounded-full bg-[var(--accent-dim)] text-[var(--accent)]">
                    {{-- ikon --}}
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <h4 class="text-2xl font-bold text-[var(--text-1)] tracking-tight">Masuk Sistem</h4>
                <p class="text-sm text-[var(--text-3)] mt-1">Penyimpanan Internal Inspektorat</p>
            </div>

            {{-- Error --}}
            @if($errors->any())
                <div class="flash flash-error" role="alert" aria-live="polite">
                    <div class="flash-icon flash-icon-error">⚠</div>
                    <div class="flash-msg">
                        <div class="font-bold mb-1">Autentikasi Gagal</div>
                        <ul class="list-disc list-inside text-sm mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label required">Alamat Email / NIP</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="form-input"
                        placeholder="Masukkan email dinas"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group" x-data="{ show: false }">
                    <label for="password" class="form-label required">Kata Sandi</label>

                    {{-- relative untuk tombol mata --}}
                    <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="password"
                            class="form-input pr-12"
                            placeholder="Masukkan kata sandi"
                            required
                        >

                        <button
                            type="button"
                            class="btn-icon absolute right-2 top-1/2 -translate-y-1/2"
                            @click="show = !show"
                            aria-label="Tampilkan / sembunyikan password"
                        >
                            {{-- icon mata hide --}}
                            <svg x-show="!show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29"/>
                            </svg>

                            {{-- icon mata show --}}
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn-primary btn-lg w-full">
                    <span class="flex items-center justify-center gap-2">
                        Masuk
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </span>
                </button>
            </form>

            <div class="text-center mt-8">
                <div class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-[var(--bg-2)] text-[var(--text-3)] border border-[var(--border)]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    Akses Terbatas Jaringan Internal
                </div>
            </div>
        </div>
    </div>
</body>
</html>