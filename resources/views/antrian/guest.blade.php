<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Antrian — Rumah Sakit Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-shadow { box-shadow: 0 8px 40px rgba(30, 64, 175, 0.12); }
        select option { color: #1f2937; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-blue-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-2xl mb-4 card-shadow">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Sistem Antrian</h1>
            <p class="text-gray-500 mt-1 font-medium">Rumah Sakit Digital</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-3xl card-shadow p-8">
            <h2 class="text-xl font-bold text-gray-700 mb-6 text-center">Ambil Nomor Antrian</h2>

            {{-- Alert area --}}
            <div id="alert-area" class="hidden mb-5 p-4 rounded-2xl text-sm font-medium"></div>

            <div class="space-y-5">

                {{-- Nama --}}
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-600 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama"
                        placeholder="Masukkan nama lengkap Anda"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500
                               transition text-gray-800 font-medium placeholder:font-normal placeholder:text-gray-400"
                        autocomplete="off"
                    >
                    <p id="nama-error" class="text-red-500 text-xs mt-1.5 hidden font-medium">⚠ Nama wajib diisi.</p>
                </div>

                {{-- Poli / Layanan --}}
                <div>
                    <label for="layanan" class="block text-sm font-semibold text-gray-600 mb-1.5">
                        Poli / Layanan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select
                            id="layanan"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500
                                   transition text-gray-800 font-medium bg-white appearance-none cursor-pointer pr-10"
                        >
                            <option value="" disabled selected class="text-gray-400">-- Pilih poli atau layanan --</option>
                            @foreach ($layananList as $poli)
                                <option value="{{ $poli }}">{{ $poli }}</option>
                            @endforeach
                        </select>
                        {{-- Chevron icon --}}
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    <p id="layanan-error" class="text-red-500 text-xs mt-1.5 hidden font-medium">⚠ Poli / Layanan wajib dipilih.</p>
                </div>

                {{-- Tombol daftar --}}
                <button
                    id="btn-daftar"
                    onclick="daftarAntrian()"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-3.5 px-6 rounded-xl
                           transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 mt-2"
                >
                    <svg id="icon-submit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <svg id="icon-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="btn-text">Ambil Nomor Antrian</span>
                </button>
            </div>

            <p class="text-center text-xs text-gray-400 mt-5 font-medium">
                Nomor antrian akan muncul di tab baru setelah mendaftar.
            </p>
        </div>

        {{-- Link papan --}}
        <div class="text-center mt-5">
            <a href="{{ route('antrian.papan') }}" target="_blank"
               class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Lihat Papan Antrian
            </a>
        </div>

    </div>

    <script>
        document.getElementById('nama').focus();

        document.getElementById('nama').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') document.getElementById('layanan').focus();
        });
        document.getElementById('layanan').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') daftarAntrian();
        });

        async function daftarAntrian() {
            const nama    = document.getElementById('nama').value.trim();
            const layanan = document.getElementById('layanan').value;

            const btnDaftar   = document.getElementById('btn-daftar');
            const btnText     = document.getElementById('btn-text');
            const iconSubmit  = document.getElementById('icon-submit');
            const iconLoading = document.getElementById('icon-loading');
            const namaError   = document.getElementById('nama-error');
            const layananError = document.getElementById('layanan-error');
            const alertArea   = document.getElementById('alert-area');

            // Reset error
            namaError.classList.add('hidden');
            layananError.classList.add('hidden');
            alertArea.classList.add('hidden');

            let valid = true;
            if (!nama) {
                namaError.classList.remove('hidden');
                document.getElementById('nama').focus();
                valid = false;
            }
            if (!layanan) {
                layananError.classList.remove('hidden');
                if (valid) document.getElementById('layanan').focus();
                valid = false;
            }
            if (!valid) return;

            // Loading
            btnDaftar.disabled = true;
            btnText.textContent = 'Memproses...';
            iconSubmit.classList.add('hidden');
            iconLoading.classList.remove('hidden');

            try {
                const response = await fetch('{{ route("antrian.register") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ nama, layanan }),
                });

                const data = await response.json();

                if (data.success) {
                    window.open(data.tiket_url, '_blank');
                    document.getElementById('nama').value = '';
                    document.getElementById('layanan').value = '';

                    alertArea.className = 'mb-5 p-4 rounded-2xl text-sm font-medium bg-green-50 text-green-700 border border-green-200';
                    alertArea.innerHTML = `
                        ✅ <strong>Berhasil!</strong> Nomor <strong>${data.nomor}</strong>
                        untuk <strong>${data.nama}</strong> — <strong>${data.layanan}</strong>.
                        Posisi antrian: <strong>${data.posisi}</strong>.
                        <br><small class="opacity-75">Tab tiket sudah dibuka.</small>
                    `;
                    alertArea.classList.remove('hidden');
                } else {
                    alertArea.className = 'mb-5 p-4 rounded-2xl text-sm font-medium bg-red-50 text-red-700 border border-red-200';
                    alertArea.textContent = data.message ?? 'Terjadi kesalahan, coba lagi.';
                    alertArea.classList.remove('hidden');
                }

            } catch (err) {
                alertArea.className = 'mb-5 p-4 rounded-2xl text-sm font-medium bg-red-50 text-red-700 border border-red-200';
                alertArea.textContent = 'Gagal terhubung ke server. Periksa koneksi Anda.';
                alertArea.classList.remove('hidden');
            } finally {
                btnDaftar.disabled = false;
                btnText.textContent = 'Ambil Nomor Antrian';
                iconSubmit.classList.remove('hidden');
                iconLoading.classList.add('hidden');
                document.getElementById('nama').focus();
            }
        }
    </script>
</body>
</html>