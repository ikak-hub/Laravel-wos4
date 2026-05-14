<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — Sistem Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .blink { animation: blink 1s step-start infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .row-hover:hover { background-color: #f0f9ff; }

        /* Tooltip loket terlambat */
        .loket-select { min-width: 80px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- ── TOP BAR ──────────────────────────────────── --}}
    <header class="bg-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-green-400" id="dot-sse"></div>
                <h1 class="text-lg font-bold tracking-tight">Dashboard Admin — Antrian</h1>
                <span id="sse-status" class="text-xs bg-blue-600 px-2 py-0.5 rounded-full">Menghubungkan…</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('antrian.papan') }}" target="_blank"
                   class="text-xs bg-blue-600 hover:bg-blue-500 px-3 py-1.5 rounded-lg transition">
                    📺 Papan Antrian
                </a>
                <button onclick="resetAntrian()"
                        class="text-xs bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg transition">
                    🗑️ Reset Antrian
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6 space-y-6">

        {{-- ── STATS BAR ─────────────────────────────── --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-medium">Menunggu</p>
                <p id="stat-menunggu" class="text-3xl font-black text-blue-600 mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-medium">Terlambat</p>
                <p id="stat-terlambat" class="text-3xl font-black text-orange-500 mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center">
                <p class="text-xs text-gray-500 font-medium">Total Daftar</p>
                <p id="stat-total" class="text-3xl font-black text-gray-700 mt-1">0</p>
            </div>
        </div>

        {{-- ── SEDANG DIPANGGIL + PANEL PANGGIL ─────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Sedang Dipanggil --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-green-600 px-5 py-3">
                    <h2 class="text-white font-semibold text-sm">🔊 Sedang Dipanggil</h2>
                </div>
                <div id="dipanggil-box" class="p-6 text-center min-h-[140px] flex flex-col items-center justify-center">
                    <p class="text-gray-400 text-sm">Belum ada panggilan</p>
                </div>
            </div>

            {{-- Panel Panggil --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-blue-600 px-5 py-3">
                    <h2 class="text-white font-semibold text-sm">📣 Panggil Nomor Berikutnya</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Arahkan ke Loket</label>
                        <select id="loket-panggil"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">Loket {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <button onclick="panggilBerikutnya()"
                            id="btn-panggil"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl
                                   transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                        📣 Panggil Nomor Berikutnya
                    </button>
                    <p class="text-xs text-gray-400 text-center">
                        Pasien berikutnya dalam antrian akan dipanggil ke loket yang dipilih.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── ANTRIAN MENUNGGU & TERLAMBAT ─────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Antrian Menunggu --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gray-700 px-5 py-3 flex items-center justify-between">
                    <h2 class="text-white font-semibold text-sm">⏳ Antrian Menunggu</h2>
                    <span id="badge-menunggu"
                          class="bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">0</span>
                </div>
                <div class="overflow-y-auto max-h-80">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">No.</th>
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-left">Daftar</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-menunggu">
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">
                                    Belum ada antrian
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Daftar Terlambat --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-orange-500 px-5 py-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-white font-semibold text-sm">⏰ Daftar Terlambat / Tidak Hadir</h2>
                        <p class="text-orange-100 text-xs mt-0.5">
                            Klik tombol <strong>Panggil Ulang</strong> untuk memanggil kembali
                        </p>
                    </div>
                    <span id="badge-terlambat"
                          class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">0</span>
                </div>
                <div class="overflow-y-auto max-h-80">
                    <table class="w-full text-sm">
                        <thead class="bg-orange-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">No.</th>
                                <th class="px-4 py-2 text-left">Nama</th>
                                <th class="px-4 py-2 text-center">Loket</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-terlambat">
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">
                                    Tidak ada pasien terlambat
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── RIWAYAT PANGGILAN ─────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gray-600 px-5 py-3">
                <h2 class="text-white font-semibold text-sm">📋 Riwayat Panggilan (5 Terakhir)</h2>
            </div>
            <div id="riwayat-box" class="p-4">
                <p class="text-gray-400 text-sm text-center">Belum ada riwayat panggilan</p>
            </div>
        </div>

    </main>

    {{-- ── TOAST NOTIFIKASI ─────────────────────────── --}}
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
        <div id="toast-inner"
             class="bg-gray-800 text-white px-5 py-3 rounded-xl shadow-xl text-sm max-w-xs">
        </div>
    </div>

    <script>
        // ── Helpers ─────────────────────────────────────────
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function showToast(msg, type = 'info') {
            const toast = document.getElementById('toast');
            const inner = document.getElementById('toast-inner');
            inner.className = `px-5 py-3 rounded-xl shadow-xl text-sm max-w-xs ${
                type === 'success' ? 'bg-green-700' :
                type === 'error'   ? 'bg-red-700'   :
                type === 'warn'    ? 'bg-orange-600' : 'bg-gray-800'
            } text-white`;
            inner.textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }

        async function post(url, body = {}) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            });
            return res.json();
        }

        // ── Panggil berikutnya ───────────────────────────────
        async function panggilBerikutnya() {
            const loket = parseInt(document.getElementById('loket-panggil').value);
            const btn   = document.getElementById('btn-panggil');
            btn.disabled = true;

            try {
                const data = await post('{{ route("antrian.panggil") }}', { loket });
                if (data.success) {
                    showToast(
                        `✅ Nomor antrian ${data.data.nomor} dipanggil ke Loket ${data.data.loket}`,
                        'success'
                    );
                } else {
                    showToast(data.message ?? 'Antrian kosong.', 'warn');
                }
            } catch {
                showToast('Gagal memanggil. Coba lagi.', 'error');
            } finally {
                setTimeout(() => { btn.disabled = false; }, 1000);
            }
        }

        // ── Tandai terlambat ─────────────────────────────────
        async function tandaiTerlambat(id, nomor, nama) {
            if (!confirm(`Tandai ${nomor} — ${nama} sebagai TIDAK HADIR?`)) return;
            const data = await post(`/admin/tandai-terlambat/${id}`);
            if (data.success) {
                showToast(`${nomor} dipindah ke daftar terlambat.`, 'warn');
            }
        }

        // ── Panggil ulang terlambat ──────────────────────────
        async function panggilTerlambat(id, nomor) {
            const loket = parseInt(
                document.getElementById(`loket-terlambat-${id}`).value
            );
            const data = await post(`/admin/panggil-terlambat/${id}`, { loket });
            if (data.success) {
                showToast(
                    `🔁 Nomor antrian ${nomor} dipanggil ulang ke Loket ${data.data.loket}`,
                    'success'
                );
            } else {
                showToast('Gagal memanggil ulang.', 'error');
            }
        }

        // ── Reset antrian ────────────────────────────────────
        async function resetAntrian() {
            if (!confirm('⚠️ Reset semua data antrian? Tindakan ini tidak bisa dibatalkan.')) return;
            const data = await post('{{ route("antrian.reset") }}');
            if (data.success) showToast('Antrian berhasil direset.', 'info');
        }

        // ── Render state ─────────────────────────────────────
        function renderState(state) {
            // Stats
            document.getElementById('stat-menunggu').textContent  = state.menunggu?.length ?? 0;
            document.getElementById('stat-terlambat').textContent = state.terlambat?.length ?? 0;
            document.getElementById('stat-total').textContent     = state.total_terdaftar ?? 0;
            document.getElementById('badge-menunggu').textContent  = state.menunggu?.length ?? 0;
            document.getElementById('badge-terlambat').textContent = state.terlambat?.length ?? 0;

            // Dipanggil
            const box = document.getElementById('dipanggil-box');
            if (state.dipanggil) {
                const d = state.dipanggil;
                const badge = d.is_terlambat
                    ? `<span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">Panggilan Ulang</span>`
                    : '';
                box.innerHTML = `
                    <div class="space-y-1">
                        ${badge}
                        <div class="text-5xl font-black text-green-600">${d.nomor}</div>
                        <div class="text-lg font-semibold text-gray-800">${d.nama}</div>
                        <div class="text-sm text-blue-700 font-medium bg-blue-50 px-4 py-1.5 rounded-lg mt-2">
                            ➡️ Nomor antrian ${d.nomor} ke Loket ${d.loket}
                        </div>
                        <div class="text-xs text-gray-400">${d.waktu}</div>
                    </div>
                `;
            } else {
                box.innerHTML = `<p class="text-gray-400 text-sm">Belum ada panggilan</p>`;
            }

            // Tabel menunggu
            const tbody = document.getElementById('tbody-menunggu');
            if (!state.menunggu?.length) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">Belum ada antrian</td></tr>`;
            } else {
                tbody.innerHTML = state.menunggu.map((item, i) => `
                    <tr class="border-t border-gray-100 row-hover">
                        <td class="px-4 py-2.5 font-bold text-blue-600">${item.nomor}</td>
                        <td class="px-4 py-2.5 text-gray-800">${item.nama}</td>
                        <td class="px-4 py-2.5 text-gray-500 text-xs">${item.waktu_daftar}</td>
                        <td class="px-4 py-2.5 text-center">
                            <button
                                onclick="tandaiTerlambat(${item.id}, '${item.nomor}', '${item.nama}')"
                                class="text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 px-2.5 py-1 rounded-lg transition font-medium">
                                Tidak Hadir
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            // Tabel terlambat
            const tbodyT = document.getElementById('tbody-terlambat');
            if (!state.terlambat?.length) {
                tbodyT.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">Tidak ada pasien terlambat</td></tr>`;
            } else {
                tbodyT.innerHTML = state.terlambat.map(item => `
                    <tr class="border-t border-gray-100 row-hover">
                        <td class="px-4 py-2.5 font-bold text-orange-600">${item.nomor}</td>
                        <td class="px-4 py-2.5 text-gray-800">${item.nama}</td>
                        <td class="px-4 py-2.5">
                            {{-- Dropdown loket per-baris terlambat --}}
                            <select id="loket-terlambat-${item.id}"
                                    class="border border-gray-300 rounded-lg px-2 py-1 text-xs loket-select
                                           focus:outline-none focus:ring-1 focus:ring-blue-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">Loket {{ $i }}</option>
                                @endfor
                            </select>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <button
                                onclick="panggilTerlambat(${item.id}, '${item.nomor}')"
                                class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2.5 py-1 rounded-lg transition font-medium">
                                Panggil Ulang
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            // Riwayat
            const riwayatBox = document.getElementById('riwayat-box');
            if (!state.riwayat?.length) {
                riwayatBox.innerHTML = `<p class="text-gray-400 text-sm text-center">Belum ada riwayat panggilan</p>`;
            } else {
                riwayatBox.innerHTML = `
                    <div class="flex flex-wrap gap-2">
                        ${state.riwayat.map((r, i) => `
                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs">
                                <span class="font-bold text-gray-700">${r.nomor}</span>
                                <span class="text-gray-500 ml-1">${r.nama}</span>
                                <span class="text-blue-600 ml-1">→ Loket ${r.loket}</span>
                                <span class="text-gray-400 ml-1">${r.waktu}</span>
                                ${r.is_terlambat ? `<span class="text-orange-500 ml-1">(ulang)</span>` : ''}
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        }

        // ── SSE Connection ───────────────────────────────────
        const dot    = document.getElementById('dot-sse');
        const sseStatus = document.getElementById('sse-status');

        // Load state awal
        fetch('{{ route("antrian.state") }}')
            .then(r => r.json())
            .then(renderState);

        // Buka SSE
        const source = new EventSource('{{ route("antrian.stream") }}');

        source.addEventListener('queue-update', function (e) {
            const state = JSON.parse(e.data);
            renderState(state);
        });

        source.onopen = function () {
            dot.className = 'w-2 h-2 rounded-full bg-green-400';
            sseStatus.textContent = 'SSE Terhubung ✓';
        };

        source.onerror = function () {
            dot.className = 'w-2 h-2 rounded-full bg-red-400 blink';
            sseStatus.textContent = 'SSE Terputus…';
        };
    </script>
</body>
</html>