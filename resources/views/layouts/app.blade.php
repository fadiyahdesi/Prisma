<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'PRISMA UHN - Portal Riset, Pengabdian & Insentif Dosen')</title>
    <meta name="description" content="PRISMA UHN adalah Portal Penelitian, Pengabdian Masyarakat, HKI, dan Insentif Dosen Universitas Harkat Negeri. Selaras BIMA Kemdiktisaintek.">
    
    <!-- Fonts: Plus Jakarta Sans for High Readability -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://img.icons8.com/color/96/prism.png">

    <!-- Styles & Scripts -->
    @if(file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="/build/{{ $cssFile }}">
        @endif
        @if($jsFile)
            <script type="module" src="/build/{{ $jsFile }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        svg {
            max-width: 100%;
            height: auto;
        }
        .w-6 { width: 1.5rem !important; }
        .h-6 { height: 1.5rem !important; }
        .w-5 { width: 1.25rem !important; }
        .h-5 { height: 1.25rem !important; }
        .w-4 { width: 1rem !important; }
        .h-4 { height: 1rem !important; }
        .w-10 { width: 2.5rem !important; }
        .h-10 { height: 2.5rem !important; }
        *, *::before, *::after {
            font-variant-numeric: normal !important;
            font-feature-settings: "zero" 0 !important;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 16px;
        }
        code, kbd, samp, pre, .font-mono, [class*="font-mono"] {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-variant-numeric: normal !important;
            font-feature-settings: "zero" 0 !important;
            letter-spacing: 0.025em;
        }
        .text-gradient, .text-gradient-blue, .text-gradient-maroon {
            background: linear-gradient(135deg, #681727 0%, #a82541 50%, #c99738 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .custom-sidebar-scroll {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .custom-sidebar-scroll::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex flex-col min-h-screen">
    @yield('content')

    {{-- Global Custom Confirmation Modal Popup (Menggantikan Dialog Konfirmasi Browser Lokal) --}}
    <div id="prisma-global-confirm-modal"
         class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         role="dialog"
         aria-modal="true"
         aria-labelledby="prisma-confirm-title">
        <div id="prisma-global-confirm-backdrop" class="fixed inset-0"></div>

        <div class="relative bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden z-10 transform transition-all">
            {{-- Header Modal --}}
            <div id="prisma-confirm-header" class="px-5 py-4 bg-rose-600 text-white flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div id="prisma-confirm-header-icon" class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <svg id="prisma-icon-danger" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <svg id="prisma-icon-warning" class="w-4 h-4 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <svg id="prisma-icon-success" class="w-4 h-4 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="prisma-confirm-title" class="font-extrabold text-sm sm:text-base leading-tight">
                            Konfirmasi Tindakan
                        </h3>
                        <p id="prisma-confirm-subtitle" class="text-[11px] text-white/85 font-medium">
                            Mohon periksa kembali sebelum melanjutkan
                        </p>
                    </div>
                </div>
                <button type="button" id="prisma-confirm-close-btn" class="text-white/80 hover:text-white p-1 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="p-5 space-y-3.5 text-xs">
                <div id="prisma-confirm-detail-box" class="hidden p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Rincian Data Terpilih</span>
                    <p id="prisma-confirm-detail-text" class="text-xs font-extrabold text-slate-900 leading-snug break-words"></p>
                </div>

                <p id="prisma-confirm-message" class="text-xs sm:text-sm font-semibold text-slate-700 leading-relaxed"></p>

                <div id="prisma-confirm-note-box" class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-semibold leading-relaxed">
                    Pastikan pilihan Anda sudah benar. Tindakan yang telah dikonfirmasi akan langsung diproses oleh sistem.
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <button type="button"
                        id="prisma-confirm-cancel-btn"
                        class="h-9 px-4 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="button"
                        id="prisma-confirm-ok-btn"
                        class="h-9 px-5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                    <span id="prisma-confirm-ok-label">Ya, Lanjutkan</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('prisma-global-confirm-modal');
            if (!modal) return;

            const header = document.getElementById('prisma-confirm-header');
            const titleEl = document.getElementById('prisma-confirm-title');
            const subtitleEl = document.getElementById('prisma-confirm-subtitle');
            const detailBox = document.getElementById('prisma-confirm-detail-box');
            const detailText = document.getElementById('prisma-confirm-detail-text');
            const messageEl = document.getElementById('prisma-confirm-message');
            const noteBox = document.getElementById('prisma-confirm-note-box');
            const okBtn = document.getElementById('prisma-confirm-ok-btn');
            const okLabel = document.getElementById('prisma-confirm-ok-label');
            const cancelBtn = document.getElementById('prisma-confirm-cancel-btn');
            const closeBtn = document.getElementById('prisma-confirm-close-btn');
            const backdrop = document.getElementById('prisma-global-confirm-backdrop');

            const iconDanger = document.getElementById('prisma-icon-danger');
            const iconWarning = document.getElementById('prisma-icon-warning');
            const iconSuccess = document.getElementById('prisma-icon-success');

            let activeCallback = null;

            function detectVariant(message, explicitVariant) {
                if (explicitVariant) return explicitVariant;
                const lower = (message || '').toLowerCase();
                if (lower.includes('hapus') || lower.includes('tolak') || lower.includes('batalkan')) {
                    return 'danger';
                }
                if (lower.includes('acc') || lower.includes('setuju') || lower.includes('bersedia') || lower.includes('pencairan') || lower.includes('pemenang') || lower.includes('tanda tangan')) {
                    return 'success';
                }
                return 'warning';
            }

            function applyVariant(variant, message, customTitle, customLabel) {
                iconDanger.classList.add('hidden');
                iconWarning.classList.add('hidden');
                iconSuccess.classList.add('hidden');

                if (variant === 'danger') {
                    header.className = 'px-5 py-4 bg-rose-600 text-white flex items-center justify-between gap-3';
                    okBtn.className = 'h-9 px-5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer';
                    noteBox.className = 'p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-semibold leading-relaxed';
                    noteBox.textContent = 'Perhatian: Data yang dihapus atau dibatalkan tidak dapat dikembalikan secara otomatis.';
                    iconDanger.classList.remove('hidden');
                    titleEl.textContent = customTitle || 'Konfirmasi Penghapusan Data';
                    subtitleEl.textContent = 'Tindakan penghapusan atau pembatalan data';
                    okLabel.textContent = customLabel || ((message || '').toLowerCase().includes('hapus') ? 'Ya, Hapus Data' : 'Ya, Batalkan');
                } else if (variant === 'success') {
                    header.className = 'px-5 py-4 bg-emerald-700 text-white flex items-center justify-between gap-3';
                    okBtn.className = 'h-9 px-5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer';
                    noteBox.className = 'p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-[11px] font-semibold leading-relaxed';
                    noteBox.textContent = 'Pastikan seluruh rincian telah sesuai sebelum menyimpan persetujuan resmi ini.';
                    iconSuccess.classList.remove('hidden');
                    titleEl.textContent = customTitle || 'Konfirmasi Persetujuan Resmi';
                    subtitleEl.textContent = 'Verifikasi persetujuan & penetapan status sistem';
                    okLabel.textContent = customLabel || 'Ya, Konfirmasi & Setujui';
                } else {
                    header.className = 'px-5 py-4 bg-[#681727] text-white flex items-center justify-between gap-3';
                    okBtn.className = 'h-9 px-5 rounded-xl bg-[#681727] hover:bg-[#52111e] text-white font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer';
                    noteBox.className = 'p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] font-semibold leading-relaxed';
                    noteBox.textContent = 'Mohon pastikan data sudah benar sebelum melanjutkan proses ini.';
                    iconWarning.classList.remove('hidden');
                    titleEl.textContent = customTitle || 'Konfirmasi Tindakan Sistem';
                    subtitleEl.textContent = 'Mohon periksa kembali sebelum melanjutkan';
                    okLabel.textContent = customLabel || 'Ya, Lanjutkan Proses';
                }
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                activeCallback = null;
            }

            function openModal(options) {
                const msg = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                const variant = detectVariant(msg, options.variant);
                applyVariant(variant, msg, options.title, options.label);

                messageEl.textContent = msg;

                if (options.detail) {
                    detailText.textContent = options.detail;
                    detailBox.classList.remove('hidden');
                } else {
                    detailText.textContent = '';
                    detailBox.classList.add('hidden');
                }

                activeCallback = options.onConfirm || null;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                okBtn.focus();
            }

            window.PrismaConfirm = { open: openModal, close: closeModal };

            okBtn.addEventListener('click', function () {
                const cb = activeCallback;
                closeModal();
                if (typeof cb === 'function') cb();
            });

            [cancelBtn, closeBtn, backdrop].forEach(function (el) {
                if (el) el.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            // Intercept klik tombol yang memiliki atribut data-confirm
            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('button[data-confirm], a[data-confirm]');
                if (!trigger || trigger.disabled) return;
                if (trigger.dataset.confirmed === 'true') {
                    trigger.dataset.confirmed = '';
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                openModal({
                    message: trigger.dataset.confirm,
                    title: trigger.dataset.confirmTitle,
                    detail: trigger.dataset.confirmDetail,
                    variant: trigger.dataset.confirmVariant,
                    label: trigger.dataset.confirmLabel,
                    onConfirm: function () {
                        const form = trigger.closest('form');
                        if (form && (trigger.type === 'submit' || !trigger.type)) {
                            form.dataset.confirmed = 'true';
                            if (typeof form.requestSubmit === 'function') {
                                form.requestSubmit(trigger);
                            } else {
                                form.submit();
                            }
                        } else if (trigger.tagName === 'A' && trigger.href) {
                            window.location.href = trigger.href;
                        } else {
                            trigger.dataset.confirmed = 'true';
                            trigger.click();
                        }
                    }
                });
            }, true);

            // Intercept submit form yang memiliki atribut data-confirm
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form || !form.dataset || !form.dataset.confirm) return;
                if (form.dataset.confirmed === 'true') {
                    form.dataset.confirmed = '';
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const submitter = e.submitter || null;
                openModal({
                    message: form.dataset.confirm,
                    title: form.dataset.confirmTitle,
                    detail: form.dataset.confirmDetail,
                    variant: form.dataset.confirmVariant,
                    label: form.dataset.confirmLabel,
                    onConfirm: function () {
                        form.dataset.confirmed = 'true';
                        if (submitter && typeof form.requestSubmit === 'function') {
                            form.requestSubmit(submitter);
                        } else {
                            form.submit();
                        }
                    }
                });
            }, true);
        })();
    </script>
</body>
</html>
