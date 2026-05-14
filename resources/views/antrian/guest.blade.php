<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7
                             20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0
                             0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Sistem Antrian</h1>
            <p class="text-gray-500 mt-1">Rumah Sakit Digital</p>
        </div>

        {{-- Card Form --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-6 text-center">Ambil Nomor Antrian</h2>

            {{-- Alert area --}}
            <div id="alert-area" class="hidden mb-4 p-4 rounded-lg text-sm font-medium"></div>

            <div class="space-y-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama"
                        placeholder="Masukkan nama Anda"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent transition text-gray-800"
                        autocomplete="off"
                    >
                    <p id="nama-error" class="text-red-500 text-xs mt-1 hidden">Nama wajib diisi.</p>
                </div>

                <button
                    id="btn-daftar"
                    onclick="daftarAntrian()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl
                           transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg id="icon-submit" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <svg id="icon-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="btn-text">Ambil Nomor Antrian</span>
                </button>
            </div>

            <p class="text-center text-xs text-gray-400 mt-4">
                Nomor antrian akan tampil di tab baru setelah mendaftar.
            </p>
        </div>

        {{-- Info link --}}
        <div class="text-center mt-4 space-x-4 text-sm text-gray-500">
            <a href="{{ route('antrian.papan') }}" target="_blank"
               class="hover:text-blue-600 transition">📺 Lihat Papan Antrian</a>
        </div>

    </div>

    <script>
        // Fokus input saat halaman load
        document.getElementById('nama').focus();

        // Enter = submit
        document.getElementById('nama').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') daftarAntrian();
        });

        async function daftarAntrian() {
            const nama      = document.getElementById('nama').value.trim();
            const btnDaftar = document.getElementById('btn-daftar');
            const btnText   = document.getElementById('btn-text');
            const iconSubmit  = document.getElementById('icon-submit');
            const iconLoading = document.getElementById('icon-loading');
            const namaError   = document.getElementById('nama-error');
            const alertArea   = document.getElementById('alert-area');

            // Validasi
            if (!nama) {
                namaError.classList.remove('hidden');
                document.getElementById('nama').focus();
                return;
            }
            namaError.classList.add('hidden');
            alertArea.classList.add('hidden');

            // Loading state
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
                    body: JSON.stringify({ nama }),
                });

                const data = await response.json();

                if (data.success) {
                    // Buka tiket di tab baru
                    window.open(data.tiket_url, '_blank');

                    // Reset form
                    document.getElementById('nama').value = '';

                    // Tampilkan sukses
                    alertArea.className = 'mb-4 p-4 rounded-lg text-sm font-medium bg-green-50 text-green-700 border border-green-200';
                    alertArea.innerHTML = `
                        ✅ <strong>Berhasil!</strong> Nomor antrian <strong>${data.nomor}</strong>
                        telah diterbitkan untuk <strong>${data.nama}</strong>.
                        Posisi antrian: <strong>${data.posisi}</strong>.
                        <br><small>Tab baru telah dibuka dengan tiket Anda.</small>
                    `;
                    alertArea.classList.remove('hidden');
                } else {
                    alertArea.className = 'mb-4 p-4 rounded-lg text-sm font-medium bg-red-50 text-red-700 border border-red-200';
                    alertArea.textContent = data.message ?? 'Terjadi kesalahan, coba lagi.';
                    alertArea.classList.remove('hidden');
                }

            } catch (err) {
                alertArea.className = 'mb-4 p-4 rounded-lg text-sm font-medium bg-red-50 text-red-700 border border-red-200';
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