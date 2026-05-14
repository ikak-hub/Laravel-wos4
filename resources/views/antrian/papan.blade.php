<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }

        /* ─── Animasi nomor baru ─── */
        @keyframes slideIn {
            from { transform: translateY(-30px); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }
        .slide-in { animation: slideIn 0.5s ease-out; }

        /* ─── Flash latar saat panggil ─── */
        @keyframes flashBg {
            0%   { background-color: #1e40af; }
            30%  { background-color: #059669; }
            60%  { background-color: #1e40af; }
            100% { background-color: #1e3a8a; }
        }
        .flash-bg { animation: flashBg 1.2s ease-out forwards; }

        /* ─── Ticker riwayat ─── */
        @keyframes tickerScroll {
            from { transform: translateX(100%); }
            to   { transform: translateX(-100%); }
        }
        .ticker-content {
            display: inline-block;
            white-space: nowrap;
            animation: tickerScroll 20s linear infinite;
        }
        .ticker-content:hover { animation-play-state: paused; }
    </style>
</head>

<body class="bg-blue-900 min-h-screen flex flex-col text-white select-none">

    {{-- ── HEADER ─────────────────────────────────────── --}}
    <header class="flex items-center justify-between px-8 py-4 bg-blue-800 shadow-lg">
        <div>
            <h1 class="text-2xl font-black tracking-tight">🏥 Rumah Sakit Digital</h1>
            <p class="text-blue-300 text-sm mt-0.5">Sistem Antrian Real-Time</p>
        </div>
        <div class="text-right">
            <div id="jam" class="text-3xl font-bold font-mono tracking-widest"></div>
            <div id="tanggal" class="text-blue-300 text-sm mt-0.5"></div>
        </div>
    </header>

    {{-- ── MAIN DISPLAY ────────────────────────────────── --}}
    <main class="flex-1 flex flex-col items-center justify-center px-8 py-6">

        {{-- Kotak utama nomor dipanggil --}}
        <div id="main-box"
             class="w-full max-w-2xl bg-blue-800 rounded-3xl shadow-2xl p-10 text-center transition-all">

            <p class="text-blue-300 text-sm font-medium uppercase tracking-widest mb-4">
                🔊 Sedang Dipanggil
            </p>

            {{-- Nomor antrian --}}
            <div id="nomor-display"
                 class="text-[120px] font-black leading-none tracking-tight text-white mb-4">
                ——
            </div>

            {{-- Nama pasien --}}
            <div id="nama-display"
                 class="text-3xl font-bold text-blue-200 mb-5 min-h-[2.5rem]">
                Menunggu Panggilan
            </div>

            {{-- Loket banner — multi-loket support --}}
            <div id="loket-display"
                 class="hidden inline-flex items-center gap-3 bg-green-500 text-white font-bold
                        text-2xl px-8 py-3 rounded-2xl shadow-lg">
                <span>➡️</span>
                <span id="loket-text">Loket 1</span>
            </div>
        </div>

        {{-- ── GRID: Antrian Menunggu + Stats ─────────── --}}
        <div class="w-full max-w-2xl mt-6 grid grid-cols-2 gap-4">

            {{-- Menunggu berikutnya --}}
            <div class="bg-blue-800 rounded-2xl p-5">
                <p class="text-blue-400 text-xs uppercase tracking-widest font-medium mb-3">
                    ⏳ Menunggu Berikutnya
                </p>
                <div id="list-menunggu" class="space-y-2">
                    <p class="text-blue-400 text-sm">Antrian kosong</p>
                </div>
            </div>

            {{-- Status & Stats --}}
            <div class="bg-blue-800 rounded-2xl p-5 space-y-3">
                <p class="text-blue-400 text-xs uppercase tracking-widest font-medium mb-1">
                    📊 Status Hari Ini
                </p>
                <div class="flex justify-between items-center">
                    <span class="text-blue-300 text-sm">Total Mendaftar</span>
                    <span id="s-total" class="font-bold text-white text-lg">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-blue-300 text-sm">Sedang Menunggu</span>
                    <span id="s-menunggu" class="font-bold text-yellow-300 text-lg">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-blue-300 text-sm">Terlambat</span>
                    <span id="s-terlambat" class="font-bold text-orange-400 text-lg">0</span>
                </div>

                {{-- SSE status --}}
                <div class="pt-2 border-t border-blue-700 flex items-center gap-2">
                    <span id="dot-sse" class="w-2 h-2 rounded-full bg-gray-400"></span>
                    <span id="sse-label" class="text-blue-400 text-xs">Menghubungkan…</span>
                </div>
            </div>
        </div>
    </main>

    {{-- ── TICKER RIWAYAT ───────────────────────────────── --}}
    <footer class="bg-blue-950 py-2.5 px-0 overflow-hidden">
        <div class="ticker-content text-blue-400 text-sm px-4" id="ticker-text">
            Selamat datang di Sistem Antrian Rumah Sakit Digital &nbsp;•&nbsp;
            Harap perhatikan layar ini dan dengarkan panggilan &nbsp;•&nbsp;
            Nomor yang telah dipanggil 3 kali dan tidak hadir akan dipindah ke daftar terlambat
        </div>
    </footer>

    {{-- ── OVERLAY: aktivasi suara pertama kali ───────────── --}}
    <div id="overlay-suara"
         class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white text-gray-800 rounded-3xl p-10 text-center max-w-sm shadow-2xl">
            <div class="text-6xl mb-4">🔊</div>
            <h2 class="text-2xl font-bold mb-2">Aktifkan Suara</h2>
            <p class="text-gray-500 text-sm mb-6">
                Tekan tombol di bawah untuk mengaktifkan notifikasi suara otomatis pada papan antrian ini.
            </p>
            <button onclick="aktivasiSuara()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl
                           transition text-lg w-full">
                ▶ Mulai & Aktifkan Suara
            </button>
        </div>
    </div>

    {{-- ── Audio ding-dong ─────────────────────────────────── --}}
    {{--
        Letakkan file dingdong.mp3 di folder public/audio/dingdong.mp3
        Anda bisa mencari file ding-dong gratis di internet (contoh: freesound.org)
        Potong audio sekitar 1-2 detik saja (bagian "ting-tong"-nya)
    --}}
    <audio id="audio-dingdong" src="{{ asset('audio/dingdong.mp3') }}" preload="auto"></audio>

    <script>
    // ── Jam digital ──────────────────────────────────────────
    function updateJam() {
        const now = new Date();
        document.getElementById('jam').textContent = now.toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
        });
        document.getElementById('tanggal').textContent = now.toLocaleDateString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
    updateJam();
    setInterval(updateJam, 1000);

    // ── State terakhir (untuk deteksi perubahan) ─────────────
    let lastNomor   = null;
    let lastLoket   = null;
    let suaraAktif  = false;
    let riwayatTicker = [];

    // ── Aktivasi suara (user gesture policy) ─────────────────
    function aktivasiSuara() {
        suaraAktif = true;
        document.getElementById('overlay-suara').remove();
        // Test audio singkat
        const audio = document.getElementById('audio-dingdong');
        audio.volume = 0.01;
        audio.play().catch(() => {});
        audio.volume = 1.0;
    }

    // ── Play suara + TTS ─────────────────────────────────────
    function bunyikanPanggilan(nomor, nama, loket) {
        if (!suaraAktif) return;
        if (!('speechSynthesis' in window)) {
            console.warn('Browser tidak mendukung Web Speech API');
            return;
        }

        window.speechSynthesis.cancel();

        const audio = document.getElementById('audio-dingdong');
        const pesan = new SpeechSynthesisUtterance(
            `Nomor antrian ${nomor}. ${nama}. Silakan menuju loket ${loket}.`
        );
        pesan.lang   = 'id-ID';
        pesan.rate   = 0.88;
        pesan.pitch  = 1.0;
        pesan.volume = 1.0;

        audio.currentTime = 0;
        audio.play().then(() => {
            audio.onended = function () {
                window.speechSynthesis.speak(pesan);
            };
        }).catch(() => {
            // Jika audio gagal, langsung TTS
            window.speechSynthesis.speak(pesan);
        });
    }

    // ── Flash animasi latar ──────────────────────────────────
    function flashMainBox() {
        const box = document.getElementById('main-box');
        box.classList.remove('flash-bg');
        void box.offsetWidth; // reflow trick
        box.classList.add('flash-bg');
    }

    // ── Render state dari SSE ─────────────────────────────────
    function renderState(state) {
        // Stats
        document.getElementById('s-total').textContent     = state.total_terdaftar ?? 0;
        document.getElementById('s-menunggu').textContent  = state.menunggu?.length ?? 0;
        document.getElementById('s-terlambat').textContent = state.terlambat?.length ?? 0;

        // Dipanggil
        const d = state.dipanggil;
        if (d) {
            const isNew = (d.nomor !== lastNomor || d.loket !== lastLoket);

            if (isNew) {
                // Update nomor & nama dengan animasi
                const nomorEl = document.getElementById('nomor-display');
                const namaEl  = document.getElementById('nama-display');
                const loketEl = document.getElementById('loket-display');
                const loketTxt = document.getElementById('loket-text');

                nomorEl.classList.remove('slide-in');
                void nomorEl.offsetWidth;
                nomorEl.classList.add('slide-in');

                nomorEl.textContent = d.nomor;
                namaEl.textContent  = d.nama;
                loketTxt.textContent = `Loket ${d.loket}`;
                loketEl.classList.remove('hidden');

                flashMainBox();
                bunyikanPanggilan(d.nomor, d.nama, d.loket);

                // Tambah ke ticker riwayat
                riwayatTicker.unshift(`Nomor ${d.nomor} — ${d.nama} → Loket ${d.loket} (${d.waktu})`);
                if (riwayatTicker.length > 10) riwayatTicker.pop();
                updateTicker();

                lastNomor = d.nomor;
                lastLoket = d.loket;
            }
        } else {
            document.getElementById('nomor-display').textContent = '——';
            document.getElementById('nama-display').textContent  = 'Menunggu Panggilan';
            document.getElementById('loket-display').classList.add('hidden');
            lastNomor = null;
            lastLoket = null;
        }

        // List menunggu (tampilkan max 5)
        const listEl = document.getElementById('list-menunggu');
        const menunggu = state.menunggu ?? [];
        if (!menunggu.length) {
            listEl.innerHTML = `<p class="text-blue-400 text-sm">Antrian kosong</p>`;
        } else {
            listEl.innerHTML = menunggu.slice(0, 5).map((item, i) => `
                <div class="flex items-center justify-between bg-blue-700/50 rounded-lg px-3 py-1.5">
                    <span class="font-bold text-white text-sm">${item.nomor}</span>
                    <span class="text-blue-300 text-sm truncate ml-2 flex-1">${item.nama}</span>
                    ${i === 0 ? `<span class="text-yellow-300 text-xs ml-2">BERIKUTNYA</span>` : ''}
                </div>
            `).join('');
            if (menunggu.length > 5) {
                listEl.innerHTML += `
                    <p class="text-blue-400 text-xs text-center mt-1">
                        + ${menunggu.length - 5} antrian lainnya
                    </p>`;
            }
        }
    }

    // ── Ticker riwayat ───────────────────────────────────────
    function updateTicker() {
        const ticker = document.getElementById('ticker-text');
        if (riwayatTicker.length) {
            ticker.textContent = riwayatTicker.join('   •   ');
        }
    }

    // ── SSE ──────────────────────────────────────────────────
    const dot      = document.getElementById('dot-sse');
    const sseLabel = document.getElementById('sse-label');

    // Load state awal
    fetch('{{ route("antrian.state") }}')
        .then(r => r.json())
        .then(state => {
            renderState(state);
            // Isi riwayat ticker dari riwayat Cache
            if (state.riwayat?.length) {
                riwayatTicker = state.riwayat.map(
                    r => `Nomor ${r.nomor} — ${r.nama} → Loket ${r.loket} (${r.waktu})`
                );
                updateTicker();
            }
        });

    const source = new EventSource('{{ route("antrian.stream") }}');

    source.addEventListener('queue-update', function (e) {
        renderState(JSON.parse(e.data));
    });

    source.onopen = function () {
        dot.className   = 'w-2 h-2 rounded-full bg-green-400';
        sseLabel.textContent = 'Terhubung ✓';
    };

    source.onerror = function () {
        dot.className   = 'w-2 h-2 rounded-full bg-red-400';
        sseLabel.textContent = 'Terputus — mencoba ulang…';
    };
    </script>
</body>
</html>