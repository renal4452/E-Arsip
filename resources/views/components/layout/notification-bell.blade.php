{{--
    resources/views/components/layout/notification-bell.blade.php
    ──────────────────────────────────────────────────────────────
    Props:
      $count (int) — jumlah notifikasi belum dibaca

    Dependensi:
      • Alpine.js v3 (sudah terpasang via layout utama)
      • Route: notifications.index   → halaman semua notifikasi
      • Route: notifications.read-all → POST tandai semua dibaca

    Cara pakai:
      <x-layout.notification-bell :count="$unreadCount" />
    ──────────────────────────────────────────────────────────────
--}}
@props(['count' => 0])

<div
    class="relative"
    x-data="notificationBell({{ $count }})"
    x-init="init()"
    @click.outside="close()"
>

    {{-- ── Tombol Lonceng ── --}}
    <button
        class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/50"
        :class="{ 'bg-slate-100 text-blue-600': open }"
        @click="toggle()"
        aria-label="Notifikasi"
        :aria-expanded="open.toString()"
        aria-haspopup="true"
    >
        <i class="bi bi-bell text-lg" aria-hidden="true"></i>

        {{-- Badge angka (tampil jika ada notif belum dibaca) --}}
        @if($count > 0)
            <span class="absolute top-1 right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white" aria-label="{{ $count }} notifikasi belum dibaca">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @else
            <span
                class="absolute top-1 right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white"
                x-show="unreadCount > 0"
                x-text="unreadCount > 99 ? '99+' : unreadCount"
                aria-live="polite"
            ></span>
        @endif
    </button>

    {{-- ── Dropdown Panel ── --}}
    <div
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 origin-top-right focus:outline-none overflow-hidden"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        role="dialog"
        aria-label="Panel notifikasi"
        @keydown.escape.window="close()"
    >

        {{-- Header dropdown --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800 text-sm">Notifikasi</span>
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100"
                    x-show="unreadCount > 0"
                    x-text="unreadCount"
                ></span>
            </div>
            <button
                class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 disabled:opacity-40 disabled:cursor-not-allowed transition-colors focus:outline-none"
                @click="markAllRead()"
                :disabled="unreadCount === 0"
                x-show="unreadCount > 0"
            >
                <i class="bi bi-check2-all text-sm" aria-hidden="true"></i>
                Tandai semua dibaca
            </button>
        </div>

        {{-- Daftar notifikasi --}}
        <div class="max-h-[380px] overflow-y-auto divide-y divide-slate-50 custom-scrollbar" role="list">

            {{-- State: Loading --}}
            <div class="flex flex-col items-center justify-center p-8 gap-2 text-slate-500" x-show="loading">
                <div class="animate-spin inline-block w-6 h-6 border-2 border-current border-t-transparent text-blue-600 rounded-full" role="status" aria-label="Memuat..."></div>
                <span class="text-xs font-medium">Memuat notifikasi…</span>
            </div>

            {{-- State: Daftar item --}}
            <template x-if="!loading && items.length > 0">
                <div class="divide-y divide-slate-50">
                    <template x-for="item in items" :key="item.id">
                        <a
                            :href="item.url ?? '#'"
                            class="group flex items-start gap-3.5 p-4 transition-colors relative hover:bg-slate-50"
                            :class="{ 'bg-blue-50/20': !item.read_at }"
                            role="listitem"
                            @click="markRead(item)"
                        >
                            {{-- Ikon semantik per tipe --}}
                            <div
                                class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-base shadow-sm"
                                :class="iconClass(item.type)"
                                aria-hidden="true"
                            >
                                <i :class="iconName(item.type)"></i>
                            </div>

                            <div class="flex-1 min-w-0 pt-0.5">
                                <p class="text-sm text-slate-700 leading-snug break-words group-hover:text-slate-900 transition-colors" :class="{ 'font-bold text-slate-900': !item.read_at }" x-text="item.data.message"></p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <time
                                        class="text-[11px] font-medium text-slate-400"
                                        :datetime="item.created_at"
                                        x-text="relativeTime(item.created_at)"
                                    ></time>
                                    <span
                                        class="px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded border"
                                        x-show="item.data.type_label"
                                        x-text="item.data.type_label"
                                        :class="badgeClass(item.type)"
                                    ></span>
                                </div>
                            </div>

                            {{-- Titik unread --}}
                            <div
                                class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-blue-600 shadow-sm shadow-blue-500/50"
                                x-show="!item.read_at"
                                aria-hidden="true"
                            ></div>
                        </a>
                    </template>
                </div>
            </template>

            {{-- State: Empty --}}
            <template x-if="!loading && items.length === 0">
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center" role="status">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 text-xl mb-3" aria-hidden="true">
                        <i class="bi bi-bell-slash"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-800">Semua sudah terbaca</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-[200px] mx-auto">Tidak ada notifikasi baru yang memerlukan tindakan saat ini.</p>
                </div>
            </template>

        </div>
        
        {{-- Footer Dropdown (Opsional: Tombol Lihat Semua) --}}
        <div class="border-t border-slate-100 p-2 bg-slate-50/50 text-center">
            <a href="{{ route('notifications.index') }}" class="block w-full py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-blue-600 hover:bg-slate-100 transition-colors">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
    function notificationBell(initialCount) {
        return {
            open:         false,
            loading:      false,
            unreadCount:  initialCount,
            items:        [],
            fetched:      false,

            init() {
                setInterval(() => this.refreshCount(), 60_000);
            },

            toggle() {
                this.open = !this.open;
                if (this.open && !this.fetched) this.fetchItems();
            },

            close() { this.open = false; },

            async fetchItems() {
                this.loading = true;
                try {
                    const res  = await fetch('/notifications/recent', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    this.items        = data.notifications ?? [];
                    this.unreadCount  = data.unread_count  ?? this.unreadCount;
                    this.fetched      = true;
                } catch (e) {
                    console.error('Gagal memuat notifikasi:', e);
                } finally {
                    this.loading = false;
                }
            },

            async refreshCount() {
                try {
                    const res  = await fetch('/notifications/count', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    this.unreadCount = data.count ?? 0;
                    if (data.count !== this.unreadCount) this.fetched = false;
                } catch {}
            },

            async markRead(item) {
                if (item.read_at) return;
                item.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                try {
                    await fetch(`/notifications/${item.id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN':    document.querySelector('meta[name=csrf-token]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                } catch {}
            },

            async markAllRead() {
                this.items.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;
                try {
                    await fetch('/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN':    document.querySelector('meta[name=csrf-token]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                } catch {}
            },

            // ── Helper Pemetaan Utilitas Tailwind CSS ──
            iconClass(type) {
                const map = {
                    approved: 'bg-emerald-50 text-emerald-600 border border-emerald-100',
                    pending:  'bg-amber-50 text-amber-600 border border-amber-100',
                    revision: 'bg-rose-50 text-rose-600 border border-rose-100',
                    info:     'bg-blue-50 text-blue-600 border border-blue-100',
                };
                return map[type] ?? 'bg-blue-50 text-blue-600 border border-blue-100';
            },

            iconName(type) {
                const map = {
                    approved: 'bi bi-check-circle-fill',
                    pending:  'bi bi-clock-fill',
                    revision: 'bi bi-exclamation-octagon-fill',
                    info:     'bi bi-info-circle-fill',
                };
                return map[type] ?? 'bi bi-bell-fill';
            },

            badgeClass(type) {
                const map = {
                    approved: 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    pending:  'bg-amber-50 text-amber-700 border-amber-100',
                    revision: 'bg-rose-50 text-rose-700 border-rose-100',
                    info:     'bg-blue-50 text-blue-700 border-blue-100',
                };
                return map[type] ?? 'bg-blue-50 text-blue-700 border-blue-100';
            },

            relativeTime(isoString) {
                const diff = Math.floor((Date.now() - new Date(isoString)) / 1000);
                if (diff < 60)       return 'Baru saja';
                if (diff < 3600)     return Math.floor(diff / 60) + ' menit lalu';
                if (diff < 86400)    return Math.floor(diff / 3600) + ' jam lalu';
                if (diff < 2592000)  return Math.floor(diff / 86400) + ' hari lalu';
                return new Date(isoString).toLocaleDateString('id-ID', {
                    day: 'numeric', month: 'short', year: 'numeric'
                });
            },
        };
    }
    </script>
    @endpush
@endonce