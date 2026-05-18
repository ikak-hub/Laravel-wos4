<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian — Klinik</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #0a1628;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            overflow: hidden;
            margin: 0;
        }

        .papan-header {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            padding: 1.2rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .papan-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .jam-digital {
            font-size: 2.2rem;
            font-weight: 900;
            font-variant-numeric: tabular-nums;
            color: #90caf9;
        }

        .papan-body {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 20px;
            padding: 20px;
            height: calc(100vh - 80px);
        }

        .panel-dipanggil {
            background: linear-gradient(135deg, #0d2137, #0a3366);
            border: 2px solid #1a73e8;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .panel-dipanggil::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(26, 115, 232, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .label-dipanggil {
            font-size: 1.2rem;
            color: #90caf9;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .nomor-dipanggil {
            font-size: 9rem;
            font-weight: 900;
            color: #ffd740;
            line-height: 1;
            text-shadow: 0 0 40px rgba(255, 215, 64, 0.4);
            transition: all 0.4s ease;
        }

        .nama-dipanggil {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-top: 0.5rem;
            transition: all 0.4s ease;
        }

        .loket-dipanggil {
            margin-top: 1rem;
            background: #1a73e8;
            padding: 0.5rem 2rem;
            border-radius: 50px;
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
        }

        @keyframes flash {
            0% {
                background: rgba(255, 215, 64, 0.3);
            }

            50% {
                background: rgba(255, 215, 64, 0.05);
            }

            100% {
                background: rgba(255, 215, 64, 0.3);
            }
        }

        .panel-dipanggil.flash-anim {
            animation: flash 0.6s ease 3;
        }

        .panel-kanan {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .panel-box {
            background: #0d2137;
            border: 1px solid #1a3a6b;
            border-radius: 16px;
            overflow: hidden;
            flex: 1;
        }

        .panel-box-header {
            background: #1a3a6b;
            padding: 0.8rem 1.2rem;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #90caf9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-box-body {
            padding: 1rem;
            overflow-y: auto;
            max-height: 250px;
        }

        .antrian-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            margin-bottom: 6px;
            border: 1px solid #1a3a6b;
        }

        .row-nomor {
            background: #1a73e8;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 3px 10px;
            border-radius: 20px;
            min-width: 60px;
            text-align: center;
        }

        .row-nama {
            font-size: 0.95rem;
            color: #cdd6f4;
            flex: 1;
        }

        .status-ind {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f44336;
            display: inline-block;
            transition: background 0.3s;
        }

        .status-ind.on {
            background: #4caf50;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.3
            }
        }

        .idle-text {
            font-size: 1.1rem;
            color: #546e7a;
            text-align: center;
            padding: 2rem 0;
        }

        #overlay-aktivasi {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #overlay-aktivasi .box {
            background: #0d47a1;
            border-radius: 20px;
            padding: 3rem 4rem;
            text-align: center;
        }

        #overlay-aktivasi h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        #btn-aktivasi {
            font-size: 1.3rem;
            padding: 1rem 3rem;
            border-radius: 50px;
            background: #ffc107;
            border: none;
            font-weight: 800;
            cursor: pointer;
            color: #000;
        }

        #btn-aktivasi:hover {
            background: #ffca28;
        }

        .badge-count {
            background: #1a73e8;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }
    </style>
</head>

<body>

    <div id="overlay-aktivasi">
        <div class="box">
            <div style="font-size:4rem;">🔊</div>
            <h3 style="color:white; margin-top:0.5rem;">Aktifkan Papan Antrian</h3>
            <p style="color:#90caf9; margin-bottom:1.5rem;">Klik tombol untuk mengaktifkan tampilan dan notifikasi suara</p>
            <button id="btn-aktivasi">▶ Mulai Papan Antrian</button>
        </div>
    </div>

    <div class="papan-header">
        <div>
            <h2>🏥 Papan Antrian — Klinik</h2>
            <small style="color:#90caf9; font-size:0.85rem;">
                <span class="status-ind" id="status-ind"></span>
                <span id="status-label" style="margin-left:6px;">Menghubungkan...</span>
            </small>
        </div>
        <div class="jam-digital" id="jam-digital">00:00:00</div>
    </div>

    <div class="papan-body">
        <div class="panel-dipanggil" id="panel-dipanggil">
            <div class="label-dipanggil">⚡ Nomor Dipanggil</div>
            <div class="nomor-dipanggil" id="disp-nomor">—</div>
            <div class="nama-dipanggil" id="disp-nama">Menunggu panggilan...</div>
            <div class="loket-dipanggil" id="disp-loket" style="visibility:hidden;">Loket —</div>
        </div>

        <div class="panel-kanan">
            <div class="panel-box">
                <div class="panel-box-header">
                    <span>📋 Antrian Menunggu</span>
                    <span class="badge-count" id="badge-menunggu">0</span>
                </div>
                <div class="panel-box-body" id="list-menunggu">
                    <div class="idle-text">Belum ada antrian</div>
                </div>
            </div>
            <div class="panel-box">
                <div class="panel-box-header">
                    <span>🔔 Riwayat Panggilan Hari Ini</span>
                </div>
                <div class="panel-box-body" id="list-riwayat">
                    <div class="idle-text">Belum ada panggilan</div>
                </div>
            </div>
        </div>
    </div>

    <audio id="audio-tingong" src="/audio/dingdong.mp3" preload="auto"></audio>

    <script>
        // ── Jam Digital ──────────────────────────────────────────────
        function updateJam() {
            const now = new Date();
            document.getElementById('jam-digital').textContent =
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0') + ':' +
                String(now.getSeconds()).padStart(2, '0');
        }
        setInterval(updateJam, 1000);
        updateJam();

        // ── State ────────────────────────────────────────────────────
        let lastNomor = null; // nomor terakhir yang dipanggil — untuk deteksi perubahan
        let audioReady = false;
        const riwayat = [];

        // ── Render Menunggu ──────────────────────────────────────────
        function renderMenunggu(list) {
            document.getElementById('badge-menunggu').textContent = list.length;
            const el = document.getElementById('list-menunggu');
            if (!list.length) {
                el.innerHTML = `<div class="idle-text">Tidak ada antrian</div>`;
                return;
            }
            el.innerHTML = list.map(a => `
        <div class="antrian-row">
            <span class="row-nomor">${a.nomor}</span>
            <span class="row-nama">${a.nama}</span>
        </div>
    `).join('');
        }

        // ── Render Riwayat ───────────────────────────────────────────
        function renderRiwayat() {
            const el = document.getElementById('list-riwayat');
            if (!riwayat.length) {
                el.innerHTML = `<div class="idle-text">Belum ada panggilan</div>`;
                return;
            }
            el.innerHTML = [...riwayat].reverse().map(r => `
        <div class="antrian-row">
            <span class="row-nomor">${r.nomor}</span>
            <span class="row-nama">${r.nama}</span>
            <small style="color:#546e7a; font-size:0.75rem;">Loket ${r.loket}</small>
        </div>
    `).join('');
        }

        // ── Update Panel Dipanggil ───────────────────────────────────
        function updatePanelDipanggil(dp) {
            document.getElementById('disp-nomor').textContent = dp.nomor;
            document.getElementById('disp-nama').textContent = dp.nama;
            const loketEl = document.getElementById('disp-loket');
            loketEl.textContent = `Silakan menuju Loket ${dp.loket}`;
            loketEl.style.visibility = 'visible';

            // Flash
            const panel = document.getElementById('panel-dipanggil');
            panel.classList.remove('flash-anim');
            void panel.offsetWidth;
            panel.classList.add('flash-anim');

            // Tambah riwayat kalau belum ada
            if (!riwayat.find(r => r.nomor === dp.nomor)) {
                riwayat.push(dp);
                renderRiwayat();
            }
        }

        // ── Suara ────────────────────────────────────────────────────
        function bunyikanDingDong() {
            return new Promise((resolve) => {
                const audio = document.getElementById('audio-tingong');
                audio.currentTime = 0;
                audio.volume = 1;
                audio.onended = () => resolve();
                audio.onerror = () => resolve(); // kalau gagal, lanjut speech
                audio.play().catch(() => resolve());
            });
        }
        // ── Suara ────────────────────────────────────────────────────
        function bunyikanPanggilan(dp) {
            if (!audioReady) return;
            if (!('speechSynthesis' in window)) return;

            window.speechSynthesis.cancel();

            const ucap = new SpeechSynthesisUtterance(
                `Nomor antrian ${dp.nomor}. ${dp.nama}. Silakan menuju Loket ${dp.loket}.`
            );
            ucap.lang = 'id-ID';
            ucap.rate = 0.85;
            ucap.pitch = 1.0;
            ucap.volume = 1.0;

            const audio = document.getElementById('audio-tingong');
            audio.currentTime = 0;
            audio.volume = 1;

            audio.play().then(() => {
                // Mulai speech 150ms sebelum audio habis
                setTimeout(() => {
                    window.speechSynthesis.speak(ucap);
                }, 3000);
            }).catch(() => {
                window.speechSynthesis.speak(ucap);
            });
        }

        // ── Polling ──────────────────────────────────────────────────
        function pollState() {
            fetch('/antrian/state')
                .then(r => r.json())
                .then(state => {
                    // Status indikator
                    document.getElementById('status-ind').classList.add('on');
                    document.getElementById('status-label').textContent = 'Terhubung — Real-Time';

                    // Render menunggu
                    renderMenunggu(state.menunggu ?? []);

                    // Cek dipanggil
                    const dp = state.dipanggil ?? null;

                    if (dp) {
                        // Kalau nomor berubah → animasi + suara
                        if (dp.nomor !== lastNomor) {
                            lastNomor = dp.nomor;
                            updatePanelDipanggil(dp);
                            bunyikanPanggilan(dp);
                        } else {
                            // Nomor sama, update display saja tanpa suara
                            updatePanelDipanggil(dp);
                        }
                    } else if (lastNomor !== null) {
                        // Tidak ada yang dipanggil (selesai/reset)
                        lastNomor = null;
                        document.getElementById('disp-nomor').textContent = '—';
                        document.getElementById('disp-nama').textContent = 'Menunggu panggilan...';
                        document.getElementById('disp-loket').style.visibility = 'hidden';
                    }
                })
                .catch(() => {
                    document.getElementById('status-ind').classList.remove('on');
                    document.getElementById('status-label').textContent = 'Terputus...';
                });
        }

        // ── Aktivasi (user gesture untuk audio) ─────────────────────
        document.getElementById('btn-aktivasi').addEventListener('click', function() {
            document.getElementById('overlay-aktivasi').style.display = 'none';
            audioReady = true;

            // Warm up audio
            const audio = document.getElementById('audio-tingong');
            audio.volume = 0.001;
            audio.play().then(() => {
                audio.pause();
                audio.volume = 1;
            }).catch(() => {});

            // Mulai polling
            pollState();
            setInterval(pollState, 2000);
        });
    </script>
</body>

</html>