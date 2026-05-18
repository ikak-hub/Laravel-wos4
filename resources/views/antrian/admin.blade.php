<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — Sistem Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .blink { animation: blink 1s step-start infinite; }
        @keyframes blink { 50% { opacity: 0; } }
        tbody tr:hover { background-color: #f8fafc; }
        @keyframes pulse-yellow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.4); }
            50%       { box-shadow: 0 0 0 8px rgba(251, 191, 36, 0); }
        }
        .pulse-badge { animation: pulse-yellow 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-800 leading-tight">Dashboard Admin</h1>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span id="dot-sse" class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                        <span id="sse-status" class="text-xs text-gray-400">Menghubungkan…</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('antrian.papan') }}" target="_blank"
                   class="text-xs font-semibold text-blue-600 border border-blue-200 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Papan Antrian
                </a>
                <a href="{{ route('antrian.guest') }}" target="_blank"
                   class="text-xs font-semibold text-gray-600 border border-gray-200 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Pendaftaran
                </a>
                <button onclick="resetAntrian()"
                        class="text-xs font-semibold text-red-600 border border-red-200 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6 space-y-5">

        {{-- BANNER DIPANGGIL --}}
        <div id="banner-dipanggil" class="hidden bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="text-5xl font-extrabold text-yellow-600 leading-none" id="banner-nomor">—</div>
                    <div>
                        <p id="banner-nama" class="text-xl font-bold text-gray-800 leading-tight"></p>
                        <p id="banner-layanan" class="text-sm text-gray-500 font-medium mt-0.5"></p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center gap-1 bg-yellow-200 text-yellow-800 text-xs font-bold px-2.5 py-0.5 rounded-full pulse-badge">
                                📣 Dipanggil
                            </span>
                            <span id="banner-badge-ulang"
                                  class="hidden inline-flex items-center gap-1 bg-orange-100 text-orange-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                🔁 Panggilan Ulang
                            </span>
                            <span id="banner-loket-waktu" class="text-xs text-gray-400 font-medium"></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button id="btn-banner-ulang" title="Panggil Ulang"
                            class="flex items-center gap-1.5 bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                        Panggil Ulang
                    </button>
                    <button id="btn-banner-selesai" title="Selesai Dilayani"
                            class="flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Selesai
                    </button>
                    <button id="btn-banner-terlambat" title="Tandai Tidak Hadir"
                            class="flex items-center gap-1.5 bg-red-400 hover:bg-red-500 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tidak Hadir
                    </button>
                </div>
            </div>
        </div>

        {{-- 4 STAT CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-500 rounded-2xl p-5 text-white shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold opacity-90">Menunggu</p>
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-menunggu" class="text-4xl font-extrabold">0</p>
            </div>
            <div class="bg-yellow-400 rounded-2xl p-5 text-white shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold opacity-90">Dipanggil</p>
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-dipanggil" class="text-4xl font-extrabold">0</p>
            </div>
            <div class="bg-red-500 rounded-2xl p-5 text-white shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold opacity-90">Terlambat</p>
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-terlambat" class="text-4xl font-extrabold">0</p>
            </div>
            <div class="bg-green-500 rounded-2xl p-5 text-white shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold opacity-90">Selesai</p>
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p id="stat-selesai" class="text-4xl font-extrabold">0</p>
            </div>
        </div>

        {{-- PANEL PANGGIL --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4">Panggil Antrian Berikutnya</h2>
            <div class="flex items-end gap-3 flex-wrap">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Loket / Poli Tujuan</label>
                    <select id="loket-panggil"
                            class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700
                                   focus:outline-none focus:border-blue-500 transition bg-white min-w-[140px]">
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">Loket {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button onclick="panggilBerikutnya()" id="btn-panggil"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl
                               transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    Panggil Berikutnya
                </button>
                <p class="text-xs text-gray-400 self-end pb-0.5">
                    Memanggil pasien pertama dalam antrian secara berurutan.
                </p>
            </div>
        </div>

        {{-- TABEL ANTRIAN --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">Daftar Antrian Hari Ini</h2>
                <span id="badge-total" class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">0 antrian</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-blue-700 text-white text-xs font-semibold uppercase tracking-wider">
                            <th class="px-5 py-3 text-left">No.</th>
                            <th class="px-5 py-3 text-left">Nama Pasien</th>
                            <th class="px-5 py-3 text-left">Poli / Layanan</th>
                            <th class="px-5 py-3 text-left">Jam Daftar</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-antrian" class="divide-y divide-gray-50">
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada antrian hari ini</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TERLAMBAT + RIWAYAT --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-orange-50">
                    <div>
                        <h2 class="text-sm font-bold text-orange-700">⏰ Daftar Tidak Hadir</h2>
                        <p class="text-xs text-orange-500 mt-0.5">Klik Panggil Ulang untuk memanggil kembali</p>
                    </div>
                    <span id="badge-terlambat" class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full">0</span>
                </div>
                <div class="overflow-y-auto max-h-64">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-2.5 text-left">No.</th>
                                <th class="px-4 py-2.5 text-left">Nama</th>
                                <th class="px-4 py-2.5 text-center">Loket</th>
                                <th class="px-4 py-2.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-terlambat" class="divide-y divide-gray-50">
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">Tidak ada</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-700">📋 Riwayat Panggilan</h2>
                </div>
                <div id="riwayat-box" class="p-4 space-y-2">
                    <p class="text-gray-400 text-sm text-center py-4">Belum ada riwayat</p>
                </div>
            </div>
        </div>

    </main>

    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden transition-all">
        <div id="toast-inner" class="px-5 py-3 rounded-xl shadow-xl text-sm font-semibold max-w-xs text-white"></div>
    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let currentDipanggil = null;

        function showToast(msg, type = 'info') {
            const t = document.getElementById('toast');
            const i = document.getElementById('toast-inner');
            const colors = { success: 'bg-green-600', error: 'bg-red-600', warn: 'bg-orange-500', info: 'bg-gray-700' };
            i.className = `px-5 py-3 rounded-xl shadow-xl text-sm font-semibold max-w-xs text-white ${colors[type] ?? colors.info}`;
            i.textContent = msg;
            t.classList.remove('hidden');
            clearTimeout(t._timer);
            t._timer = setTimeout(() => t.classList.add('hidden'), 3500);
        }

        async function post(url, body = {}) {
            const r = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            return r.json();
        }

        function getLoket() {
            return parseInt(document.getElementById('loket-panggil').value);
        }

        async function panggilBerikutnya() {
            const btn = document.getElementById('btn-panggil');
            btn.disabled = true;
            try {
                const data = await post('{{ route("antrian.panggil") }}', { loket: getLoket() });
                if (data.success) {
                    showToast(`✅ ${data.data.nomor} — ${data.data.nama} dipanggil ke Loket ${data.data.loket}`, 'success');
                    fetchState();
                } else {
                    showToast(data.message ?? 'Antrian kosong.', 'warn');
                }
            } catch { showToast('Gagal memanggil.', 'error'); }
            finally { setTimeout(() => { btn.disabled = false; }, 1000); }
        }

        async function panggilLangsung(id, nomor, nama) {
            const data = await post(`/admin/panggil-langsung/${id}`, { loket: getLoket() });
            if (data.success) {
                showToast(`✅ ${nomor} — ${nama} dipanggil ke Loket ${data.data.loket}`, 'success');
                fetchState();
            } else {
                showToast(data.message ?? 'Gagal.', 'error');
            }
        }

        async function tandaiSelesai() {
            if (!currentDipanggil) return;
            const data = await post(`/admin/selesai/${currentDipanggil.id}`);
            if (data.success) { showToast(`✅ ${currentDipanggil.nomor} selesai dilayani.`, 'success'); fetchState(); }
            else showToast('Gagal menandai selesai.', 'error');
        }

        async function panggilUlang() {
            if (!currentDipanggil) return;
            await post(`/admin/tandai-terlambat/${currentDipanggil.id}`);
            const data = await post(`/admin/panggil-terlambat/${currentDipanggil.id}`, { loket: getLoket() });
            if (data.success) { showToast(`🔁 ${currentDipanggil.nomor} dipanggil ulang ke Loket ${data.data.loket}`, 'success'); fetchState(); }
        }

        async function tandaiTerlambatBanner() {
            if (!currentDipanggil) return;
            const { id, nomor, nama } = currentDipanggil;
            if (!confirm(`Tandai ${nomor} — ${nama} sebagai TIDAK HADIR?`)) return;
            const data = await post(`/admin/tandai-terlambat/${id}`);
            if (data.success) { showToast(`⏰ ${nomor} ditandai tidak hadir.`, 'warn'); fetchState(); }
        }

        async function tandaiTerlambat(id, nomor, nama) {
            if (!confirm(`Tandai ${nomor} — ${nama} sebagai TIDAK HADIR?`)) return;
            const data = await post(`/admin/tandai-terlambat/${id}`);
            if (data.success) { showToast(`⏰ ${nomor} ditandai tidak hadir.`, 'warn'); fetchState(); }
        }

        async function panggilTerlambat(id, nomor) {
            const loket = parseInt(document.getElementById(`loket-tl-${id}`).value);
            const data  = await post(`/admin/panggil-terlambat/${id}`, { loket });
            if (data.success) { showToast(`🔁 ${nomor} dipanggil ulang ke Loket ${data.data.loket}`, 'success'); fetchState(); }
            else showToast('Gagal.', 'error');
        }

        async function resetAntrian() {
            if (!confirm('⚠️ Reset semua data antrian? Tidak bisa dibatalkan!')) return;
            const data = await post('{{ route("antrian.reset") }}');
            if (data.success) { showToast('Antrian direset.', 'info'); fetchState(); }
        }

        document.getElementById('btn-banner-ulang').onclick     = panggilUlang;
        document.getElementById('btn-banner-selesai').onclick   = tandaiSelesai;
        document.getElementById('btn-banner-terlambat').onclick = tandaiTerlambatBanner;

        function statusBadge(status) {
            const map = {
                dipanggil: `<span class="inline-flex items-center bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded-full">Dipanggil</span>`,
                menunggu:  `<span class="inline-flex items-center bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">Menunggu</span>`,
                terlambat: `<span class="inline-flex items-center bg-orange-100 text-orange-700 text-xs font-semibold px-2.5 py-1 rounded-full">Terlambat</span>`,
                selesai:   `<span class="inline-flex items-center bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">Selesai</span>`,
            };
            return map[status] ?? map.menunggu;
        }

        function renderState(state) {
            currentDipanggil = state.dipanggil;

            const menunggu  = state.menunggu  ?? [];
            const terlambat = state.terlambat ?? [];
            const selesai   = state.selesai   ?? [];
            const dp        = state.dipanggil;

            document.getElementById('stat-menunggu').textContent  = menunggu.length;
            document.getElementById('stat-dipanggil').textContent = dp ? 1 : 0;
            document.getElementById('stat-terlambat').textContent = terlambat.length;
            document.getElementById('stat-selesai').textContent   = selesai.length;

            const total = menunggu.length + (dp ? 1 : 0) + terlambat.length + selesai.length;
            document.getElementById('badge-total').textContent = `${total} antrian`;

            const banner = document.getElementById('banner-dipanggil');
            if (dp) {
                banner.classList.remove('hidden');
                document.getElementById('banner-nomor').textContent   = dp.nomor;
                document.getElementById('banner-nama').textContent    = dp.nama;
                document.getElementById('banner-layanan').textContent = dp.layanan ?? 'Umum';
                document.getElementById('banner-loket-waktu').textContent = `Loket ${dp.loket} • ${dp.waktu}`;
                const badgeUlang = document.getElementById('banner-badge-ulang');
                dp.is_terlambat ? badgeUlang.classList.remove('hidden') : badgeUlang.classList.add('hidden');
            } else {
                banner.classList.add('hidden');
            }

            const allRows = [
                ...menunggu.map(i => ({ ...i, _s: 'menunggu' })),
                ...(dp ? [{ ...dp, _s: 'dipanggil', waktu_daftar: dp.waktu_daftar ?? dp.waktu }] : []),
                ...terlambat.map(i => ({ ...i, _s: 'terlambat' })),
                ...selesai.map(i => ({ ...i, _s: 'selesai' })),
            ].sort((a, b) => (a.nomor ?? '').localeCompare(b.nomor ?? ''));

            const tbody = document.getElementById('tbody-antrian');
            if (!allRows.length) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada antrian hari ini</td></tr>`;
            } else {
                tbody.innerHTML = allRows.map(item => {
                    let aksi = `<span class="text-gray-300 text-xs">—</span>`;
                    if (item._s === 'dipanggil') {
                        aksi = `<div class="flex justify-center items-center gap-1.5">
                            <button onclick="panggilUlang()" title="Panggil Ulang" class="w-8 h-8 flex items-center justify-center bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            </button>
                            <button onclick="tandaiSelesai()" title="Selesai" class="w-8 h-8 flex items-center justify-center bg-green-100 hover:bg-green-200 text-green-600 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <button onclick="tandaiTerlambatBanner()" title="Tidak Hadir" class="w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 text-red-500 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                        </div>`;
                    } else if (item._s === 'menunggu') {
                        aksi = `<button onclick="panggilLangsung(${item.id}, '${item.nomor}', '${item.nama}')" title="Panggil Langsung" class="w-8 h-8 flex items-center justify-center mx-auto bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </button>`;
                    } else if (item._s === 'terlambat') {
                        aksi = `<button onclick="panggilTerlambat(${item.id}, '${item.nomor}')" title="Panggil Ulang" class="text-xs font-semibold bg-blue-100 hover:bg-blue-200 text-blue-700 px-2.5 py-1.5 rounded-lg transition mx-auto block">Panggil Ulang</button>`;
                    }

                    return `<tr class="transition-colors">
                        <td class="px-5 py-3.5 font-bold text-gray-800">${item.nomor}</td>
                        <td class="px-5 py-3.5 font-semibold text-gray-700">${item.nama}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs font-medium">${item.layanan ?? 'Umum'}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs font-medium">${item.waktu_daftar ?? item.waktu ?? ''}</td>
                        <td class="px-5 py-3.5 text-center">${statusBadge(item._s)}</td>
                        <td class="px-5 py-3.5 text-center">${aksi}</td>
                    </tr>`;
                }).join('');
            }

            document.getElementById('badge-terlambat').textContent = terlambat.length;
            const tbodyT = document.getElementById('tbody-terlambat');
            if (!terlambat.length) {
                tbodyT.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">Tidak ada</td></tr>`;
            } else {
                tbodyT.innerHTML = terlambat.map(item => `
                    <tr class="transition-colors">
                        <td class="px-4 py-3 font-bold text-orange-600 text-sm">${item.nomor}</td>
                        <td class="px-4 py-3 text-gray-700 text-sm font-medium">${item.nama}</td>
                        <td class="px-4 py-3">
                            <select id="loket-tl-${item.id}" class="border border-gray-300 rounded-lg px-2 py-1 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-blue-400 w-20">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">Loket {{ $i }}</option>
                                @endfor
                            </select>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="panggilTerlambat(${item.id}, '${item.nomor}')" class="text-xs font-semibold bg-blue-100 hover:bg-blue-200 text-blue-700 px-2.5 py-1.5 rounded-lg transition">Panggil Ulang</button>
                        </td>
                    </tr>`).join('');
            }

            const rBox = document.getElementById('riwayat-box');
            if (!state.riwayat?.length) {
                rBox.innerHTML = `<p class="text-gray-400 text-sm text-center py-4">Belum ada riwayat</p>`;
            } else {
                rBox.innerHTML = state.riwayat.map(r => `
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 py-2.5 text-xs">
                        <span class="font-bold text-gray-700 w-12">${r.nomor}</span>
                        <span class="text-gray-600 flex-1 truncate mx-2">${r.nama}</span>
                        <span class="text-blue-600 font-semibold">L${r.loket}</span>
                        <span class="text-gray-400 ml-2">${r.waktu}</span>
                        ${r.is_terlambat ? `<span class="text-orange-400 ml-1 font-medium">(ulang)</span>` : ''}
                    </div>`).join('');
            }
        }

        // ── POLLING (bukan SSE) ───────────────────────────────
        const dot       = document.getElementById('dot-sse');
        const sseStatus = document.getElementById('sse-status');

        function fetchState() {
            fetch('/antrian/state')
                .then(r => r.json())
                .then(state => {
                    renderState(state);
                    dot.className   = 'w-1.5 h-1.5 rounded-full bg-green-400';
                    sseStatus.textContent = 'Terhubung ✓';
                })
                .catch(() => {
                    dot.className   = 'w-1.5 h-1.5 rounded-full bg-red-400 blink';
                    sseStatus.textContent = 'Terputus…';
                });
        }

        // Load pertama kali
        fetchState();

        // Polling setiap 2 detik
        setInterval(fetchState, 2000);
    </script>
</body>
</html>