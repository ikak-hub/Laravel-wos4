<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian {{ $antrian->nomor_antrian }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        {{-- Tiket Card --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Header strip --}}
            <div class="bg-blue-600 px-6 py-4 text-white text-center">
                <p class="text-xs font-medium tracking-widest uppercase opacity-80">Rumah Sakit Digital</p>
                <p class="font-semibold text-sm mt-1">Tiket Antrian Poliklinik</p>
            </div>

            {{-- Nomor besar --}}
            <div class="px-6 py-8 text-center border-b border-dashed border-gray-200">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">Nomor Antrian Anda</p>
                <div class="text-8xl font-black text-blue-600 leading-none tracking-tight">
                    {{ $antrian->nomor_antrian }}
                </div>
            </div>

            {{-- Info pasien --}}
            <div class="px-6 py-5 space-y-3 border-b border-dashed border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-medium">Nama Pasien</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $antrian->nama }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-medium">Waktu Daftar</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $antrian->created_at->format('H:i') }} WIB</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-medium">Tanggal</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $antrian->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                </div>
                @if($posisi !== null)
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-medium">Posisi Antrian</span>
                    <span class="text-sm font-semibold text-blue-600">{{ $posisi }} dari depan</span>
                </div>
                @endif
            </div>

            {{-- Instruksi --}}
            <div class="px-6 py-4 bg-blue-50">
                <p class="text-xs text-blue-700 text-center leading-relaxed">
                    ⚠️ Harap perhatikan layar antrian dan dengarkan panggilan.
                    Jika nama dipanggil, segera menuju loket yang disebutkan.
                </p>
            </div>

            {{-- Status realtime --}}
            <div id="status-box" class="px-6 py-3 bg-gray-50 text-center">
                <p id="status-text" class="text-xs text-gray-500">
                    🔄 Menghubungkan ke sistem antrian...
                </p>
            </div>

        </div>

        {{-- Actions --}}
        <div class="mt-4 space-y-2 no-print">
            <button onclick="window.print()"
                class="w-full bg-white border border-gray-300 text-gray-700 font-medium py-2.5 px-4 rounded-xl
                       hover:bg-gray-50 transition text-sm flex items-center justify-center gap-2">
                🖨️ Cetak Tiket
            </button>
            <a href="{{ route('antrian.papan') }}" target="_blank"
               class="block w-full bg-blue-600 text-white font-medium py-2.5 px-4 rounded-xl
                      hover:bg-blue-700 transition text-sm text-center">
                📺 Lihat Papan Antrian
            </a>
        </div>

        <p class="text-center text-xs text-gray-400 mt-4">
            Anda bisa menutup tab ini kapan saja.
        </p>
    </div>

    {{-- SSE: pantau apakah nomor ini sudah dipanggil --}}
    <script>
        const nomorSaya = '{{ $antrian->nomor_antrian }}';
        const statusText = document.getElementById('status-text');
        const statusBox  = document.getElementById('status-box');

        const source = new EventSource('{{ route("antrian.stream") }}');

        source.addEventListener('queue-update', function (e) {
            const state = JSON.parse(e.data);

            // Cek apakah nomor ini sedang dipanggil
            if (state.dipanggil && state.dipanggil.nomor === nomorSaya) {
                statusBox.className = 'px-6 py-3 bg-green-100 text-center animate-pulse';
                statusText.className = 'text-sm font-bold text-green-700';
                statusText.textContent =
                    `🔔 Nomor antrian ${nomorSaya} — silakan menuju Loket ${state.dipanggil.loket}!`;
                return;
            }

            // Hitung posisi dalam antrian
            const menunggu = state.menunggu ?? [];
            const idx = menunggu.findIndex(item => item.nomor === nomorSaya);

            if (idx >= 0) {
                statusBox.className = 'px-6 py-3 bg-gray-50 text-center';
                statusText.className = 'text-xs text-gray-600';
                const posisi = idx + 1;
                statusText.textContent = `⏳ Posisi antrian: ${posisi} — ${posisi > 1 ? 'Harap sabar menunggu.' : 'Anda berikutnya!'}`;
            } else {
                // Sudah dipanggil atau tidak ada dalam list menunggu
                const riwayat = state.riwayat ?? [];
                const sudahDipanggil = riwayat.some(r => r.nomor === nomorSaya);
                if (sudahDipanggil) {
                    statusText.className = 'text-xs text-gray-400';
                    statusText.textContent = '✅ Nomor Anda telah dipanggil.';
                }
            }
        });

        source.onerror = function () {
            statusText.textContent = '⚠️ Koneksi ke server terputus. Coba refresh halaman.';
        };
    </script>
</body>
</html>