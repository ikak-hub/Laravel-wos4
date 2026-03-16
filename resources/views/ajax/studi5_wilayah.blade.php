@extends('layouts.app')

@section('content')
@include('layouts.header', ['title' => 'AJAX – Cascading Wilayah', 'icon' => 'mdi-map-marker-multiple'])

{{-- ══════════════════════════════════════════════════════════════════
     Tab pilih versi: jQuery Ajax vs Axios
══════════════════════════════════════════════════════════════════ --}}
<ul class="nav nav-tabs mb-4" id="versionTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="ajax-tab" data-bs-toggle="tab" data-bs-target="#panel-ajax">
            <i class="mdi mdi-jquery me-1"></i> jQuery Ajax
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="axios-tab" data-bs-toggle="tab" data-bs-target="#panel-axios">
            <i class="mdi mdi-lightning-bolt me-1"></i> Axios
        </button>
    </li>
</ul>

<div class="tab-content" id="versionTabContent">

    {{-- ══════════════════ TAB 1: jQuery Ajax ══════════════════ --}}
    <div class="tab-pane fade show active" id="panel-ajax">
        <div class="row">
            <div class="col-lg-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white py-2">
                        <i class="mdi mdi-map me-1"></i> Cascading Wilayah — jQuery Ajax
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">
                            Pilih Provinsi → Kota/Kabupaten → Kecamatan → Kelurahan secara berurutan.
                            Data diambil dari server menggunakan <strong>$.ajax()</strong> jQuery.
                        </p>

                        {{-- ── Provinsi ── --}}
                        <div class="form-group mb-3">
                            <label class="fw-semibold">Provinsi <span class="text-danger">*</span></label>
                            <select id="aj-provinsi" class="form-select">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                            <div id="aj-load-provinsi" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat provinsi...
                            </div>
                        </div>

                        {{-- ── Kota ── --}}
                        <div class="form-group mb-3">
                            <label class="fw-semibold">Kota / Kabupaten <span class="text-danger">*</span></label>
                            <select id="aj-kota" class="form-select" disabled>
                                <option value="">-- Pilih Kota --</option>
                            </select>
                            <div id="aj-load-kota" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat kota...
                            </div>
                        </div>

                        {{-- ── Kecamatan ── --}}
                        <div class="form-group mb-3">
                            <label class="fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                            <select id="aj-kecamatan" class="form-select" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                            <div id="aj-load-kecamatan" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat kecamatan...
                            </div>
                        </div>

                        {{-- ── Kelurahan ── --}}
                        <div class="form-group mb-4">
                            <label class="fw-semibold">Kelurahan / Desa <span class="text-danger">*</span></label>
                            <select id="aj-kelurahan" class="form-select" disabled>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                            <div id="aj-load-kelurahan" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat kelurahan...
                            </div>
                        </div>

                        {{-- ── Hasil pilihan ── --}}
                        <div class="p-3 bg-light rounded border" id="aj-result-box" style="display:none;">
                            <p class="fw-semibold mb-2 text-muted small">Alamat Terpilih:</p>
                            <p id="aj-result" class="mb-0 fs-6 text-success fw-bold"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Penjelasan kode jQuery Ajax ── --}}
            <div class="col-lg-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-dark text-white py-2">
                        <i class="mdi mdi-code-tags me-1"></i> Cara Kerja — jQuery Ajax
                    </div>
                    <div class="card-body small">
                        <p class="fw-semibold mb-1">Konstruksi $.ajax():</p>
                        <pre class="bg-dark text-light p-2 rounded small" style="font-size:11px;">$.ajax({
  url: "/ajax/wilayah/kota/" + id,
  type: "GET",
  success: function(response) {
    // response.data berisi array kota
    if (response.status === "success") {
      response.data.forEach(item => {
        $('#aj-kota').append(
          `&lt;option value="${item.id}"&gt;
             ${item.nama}
           &lt;/option&gt;`
        );
      });
    }
  },
  error: function(xhr) {
    console.log(xhr);
  }
});</pre>
                        <hr>
                        <ol class="ps-3" style="line-height:1.9">
                            <li>Saat halaman load → AJAX ambil <strong>provinsi</strong></li>
                            <li>Pilih Provinsi → AJAX ambil <strong>kota</strong></li>
                            <li>Pilih Kota → AJAX ambil <strong>kecamatan</strong> + reset kelurahan</li>
                            <li>Pilih Kecamatan → AJAX ambil <strong>kelurahan</strong></li>
                            <li>Pilih Kelurahan → tampilkan alamat lengkap</li>
                            <li>Ubah level manapun → clear level di bawahnya</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- /tab ajax --}}

    {{-- ══════════════════ TAB 2: Axios ══════════════════ --}}
    <div class="tab-pane fade" id="panel-axios">
        <div class="row">
            <div class="col-lg-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-gradient-info text-white py-2">
                        <i class="mdi mdi-map me-1"></i> Cascading Wilayah — Axios
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">
                            Fungsi identik dengan versi jQuery Ajax, namun menggunakan library
                            <strong>Axios</strong> yang berbasis <em>Promise</em>.
                        </p>

                        <div class="form-group mb-3">
                            <label class="fw-semibold">Provinsi <span class="text-danger">*</span></label>
                            <select id="ax-provinsi" class="form-select">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                            <div id="ax-load-provinsi" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat...
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="fw-semibold">Kota / Kabupaten <span class="text-danger">*</span></label>
                            <select id="ax-kota" class="form-select" disabled>
                                <option value="">-- Pilih Kota --</option>
                            </select>
                            <div id="ax-load-kota" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat...
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                            <select id="ax-kecamatan" class="form-select" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                            <div id="ax-load-kecamatan" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat...
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="fw-semibold">Kelurahan / Desa <span class="text-danger">*</span></label>
                            <select id="ax-kelurahan" class="form-select" disabled>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                            <div id="ax-load-kelurahan" class="text-muted small mt-1 d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memuat...
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded border" id="ax-result-box" style="display:none;">
                            <p class="fw-semibold mb-2 text-muted small">Alamat Terpilih:</p>
                            <p id="ax-result" class="mb-0 fs-6 text-info fw-bold"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header bg-dark text-white py-2">
                        <i class="mdi mdi-code-tags me-1"></i> Cara Kerja — Axios
                    </div>
                    <div class="card-body small">
                        <p class="fw-semibold mb-1">Konstruksi Axios dengan Promise:</p>
                        <pre class="bg-dark text-light p-2 rounded small" style="font-size:11px;">axios({
  method: 'GET',
  url: '/ajax/wilayah/kota/' + id
})
.then(function(response) {
  // response.data adalah JSON dari Laravel
  const data = response.data.data;
  data.forEach(item => {
    $('#ax-kota').append(
      `&lt;option value="${item.id}"&gt;
         ${item.nama}
       &lt;/option&gt;`
    );
  });
})
.catch(function(error) {
  console.log(error);
});</pre>
                        <hr>
                        <p class="fw-semibold mb-1">Perbedaan Axios vs Ajax:</p>
                        <ul class="ps-3" style="line-height:1.9">
                            <li>Axios: <code>response.data</code> → JSON sudah di-parse otomatis</li>
                            <li>Ajax: <code>response</code> → sudah berupa objek JS</li>
                            <li>Axios pakai <code>.then() / .catch()</code> (Promise)</li>
                            <li>Ajax pakai <code>success / error</code> callback</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- /tab axios --}}

</div>{{-- /tab-content --}}
@endsection

@push('scripts')
{{-- Axios CDN --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
/* ════════════════════════════════════════════════════════════════════
   HELPER: reset select ke placeholder dan disable
══════════════════════════════════════════════════════════════════════ */
function resetSelect(selector, placeholder) {
    $(selector).html(`<option value="">${placeholder}</option>`).prop('disabled', true);
}

/* ════════════════════════════════════════════════════════════════════
   ▌TAB 1 — jQuery Ajax
══════════════════════════════════════════════════════════════════════ */
$(function () {

    // ── Load provinsi saat halaman pertama dibuka ──────────────────
    $('#aj-load-provinsi').removeClass('d-none');

    $.ajax({
        url: "{{ route('ajax.wilayah.provinsi') }}",
        type: "GET",
        success: function (response) {
            $('#aj-load-provinsi').addClass('d-none');
            if (response.status === 'success') {
                response.data.forEach(function (item) {
                    $('#aj-provinsi').append(
                        `<option value="${item.id}">${item.nama}</option>`
                    );
                });
            }
        },
        error: function (xhr) {
            $('#aj-load-provinsi').addClass('d-none');
            console.log('Gagal ambil provinsi:', xhr);
        }
    });

    // ── Provinsi berubah → load kota, reset kecamatan & kelurahan ──
    $('#aj-provinsi').on('change', function () {
        const idProvinsi = $(this).val();
        resetSelect('#aj-kota', '-- Pilih Kota --');
        resetSelect('#aj-kecamatan', '-- Pilih Kecamatan --');
        resetSelect('#aj-kelurahan', '-- Pilih Kelurahan --');
        $('#aj-result-box').hide();

        if (!idProvinsi) return;

        $('#aj-load-kota').removeClass('d-none');

        $.ajax({
            url: "{{ url('/ajax/wilayah/kota') }}/" + idProvinsi,
            type: "GET",
            success: function (response) {
                $('#aj-load-kota').addClass('d-none');
                if (response.status === 'success') {
                    $('#aj-kota').prop('disabled', false);
                    response.data.forEach(function (item) {
                        $('#aj-kota').append(
                            `<option value="${item.id}">${item.nama}</option>`
                        );
                    });
                }
            },
            error: function (xhr) {
                $('#aj-load-kota').addClass('d-none');
                console.log('Gagal ambil kota:', xhr);
            }
        });
    });

    // ── Kota berubah → load kecamatan, reset kelurahan ─────────────
    $('#aj-kota').on('change', function () {
        const idKota = $(this).val();
        resetSelect('#aj-kecamatan', '-- Pilih Kecamatan --');
        resetSelect('#aj-kelurahan', '-- Pilih Kelurahan --');
        $('#aj-result-box').hide();

        if (!idKota) return;

        $('#aj-load-kecamatan').removeClass('d-none');

        $.ajax({
            url: "{{ url('/ajax/wilayah/kecamatan') }}/" + idKota,
            type: "GET",
            success: function (response) {
                $('#aj-load-kecamatan').addClass('d-none');
                if (response.status === 'success') {
                    $('#aj-kecamatan').prop('disabled', false);
                    response.data.forEach(function (item) {
                        $('#aj-kecamatan').append(
                            `<option value="${item.id}">${item.nama}</option>`
                        );
                    });
                }
            },
            error: function (xhr) {
                $('#aj-load-kecamatan').addClass('d-none');
                console.log('Gagal ambil kecamatan:', xhr);
            }
        });
    });

    // ── Kecamatan berubah → load kelurahan ─────────────────────────
    $('#aj-kecamatan').on('change', function () {
        const idKec = $(this).val();
        resetSelect('#aj-kelurahan', '-- Pilih Kelurahan --');
        $('#aj-result-box').hide();

        if (!idKec) return;

        $('#aj-load-kelurahan').removeClass('d-none');

        $.ajax({
            url: "{{ url('/ajax/wilayah/kelurahan') }}/" + idKec,
            type: "GET",
            success: function (response) {
                $('#aj-load-kelurahan').addClass('d-none');
                if (response.status === 'success') {
                    $('#aj-kelurahan').prop('disabled', false);
                    response.data.forEach(function (item) {
                        $('#aj-kelurahan').append(
                            `<option value="${item.id}">${item.nama}</option>`
                        );
                    });
                }
            },
            error: function (xhr) {
                $('#aj-load-kelurahan').addClass('d-none');
                console.log('Gagal ambil kelurahan:', xhr);
            }
        });
    });

    // ── Kelurahan berubah → tampilkan alamat lengkap ───────────────
    $('#aj-kelurahan').on('change', function () {
        if (!$(this).val()) { $('#aj-result-box').hide(); return; }

        const prov = $('#aj-provinsi option:selected').text();
        const kota = $('#aj-kota option:selected').text();
        const kec  = $('#aj-kecamatan option:selected').text();
        const kel  = $(this).find('option:selected').text();

        $('#aj-result').text(`${kel}, Kec. ${kec}, ${kota}, ${prov}`);
        $('#aj-result-box').show();
    });

});

/* ════════════════════════════════════════════════════════════════════
   ▌TAB 2 — Axios
══════════════════════════════════════════════════════════════════════ */
(function () {

    // Set CSRF header global untuk semua request Axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Load provinsi saat tab Axios pertama kali aktif ────────────
    let axProvinsiLoaded = false;

    document.getElementById('axios-tab').addEventListener('shown.bs.tab', function () {
        if (axProvinsiLoaded) return;
        axProvinsiLoaded = true;

        document.getElementById('ax-load-provinsi').classList.remove('d-none');

        axios({
            method: 'GET',
            url: "{{ route('ajax.wilayah.provinsi') }}"
        })
        .then(function (response) {
            document.getElementById('ax-load-provinsi').classList.add('d-none');
            const data = response.data.data; // response.data = JSON Laravel
            data.forEach(function (item) {
                const opt = new Option(item.nama, item.id);
                document.getElementById('ax-provinsi').appendChild(opt);
            });
        })
        .catch(function (error) {
            document.getElementById('ax-load-provinsi').classList.add('d-none');
            console.log('Gagal ambil provinsi (Axios):', error);
        });
    });

    // ── Provinsi ───────────────────────────────────────────────────
    document.getElementById('ax-provinsi').addEventListener('change', function () {
        const id = this.value;
        resetSelect('#ax-kota', '-- Pilih Kota --');
        resetSelect('#ax-kecamatan', '-- Pilih Kecamatan --');
        resetSelect('#ax-kelurahan', '-- Pilih Kelurahan --');
        document.getElementById('ax-result-box').style.display = 'none';

        if (!id) return;

        document.getElementById('ax-load-kota').classList.remove('d-none');

        axios.get("{{ url('/ajax/wilayah/kota') }}/" + id)
        .then(function (response) {
            document.getElementById('ax-load-kota').classList.add('d-none');
            const data = response.data.data;
            const sel = document.getElementById('ax-kota');
            sel.disabled = false;
            data.forEach(function (item) {
                sel.appendChild(new Option(item.nama, item.id));
            });
        })
        .catch(function (error) {
            document.getElementById('ax-load-kota').classList.add('d-none');
            console.log('Gagal ambil kota (Axios):', error);
        });
    });

    // ── Kota ───────────────────────────────────────────────────────
    document.getElementById('ax-kota').addEventListener('change', function () {
        const id = this.value;
        resetSelect('#ax-kecamatan', '-- Pilih Kecamatan --');
        resetSelect('#ax-kelurahan', '-- Pilih Kelurahan --');
        document.getElementById('ax-result-box').style.display = 'none';

        if (!id) return;

        document.getElementById('ax-load-kecamatan').classList.remove('d-none');

        axios.get("{{ url('/ajax/wilayah/kecamatan') }}/" + id)
        .then(function (response) {
            document.getElementById('ax-load-kecamatan').classList.add('d-none');
            const data = response.data.data;
            const sel = document.getElementById('ax-kecamatan');
            sel.disabled = false;
            data.forEach(function (item) {
                sel.appendChild(new Option(item.nama, item.id));
            });
        })
        .catch(function (error) {
            document.getElementById('ax-load-kecamatan').classList.add('d-none');
            console.log('Gagal ambil kecamatan (Axios):', error);
        });
    });

    // ── Kecamatan ──────────────────────────────────────────────────
    document.getElementById('ax-kecamatan').addEventListener('change', function () {
        const id = this.value;
        resetSelect('#ax-kelurahan', '-- Pilih Kelurahan --');
        document.getElementById('ax-result-box').style.display = 'none';

        if (!id) return;

        document.getElementById('ax-load-kelurahan').classList.remove('d-none');

        axios.get("{{ url('/ajax/wilayah/kelurahan') }}/" + id)
        .then(function (response) {
            document.getElementById('ax-load-kelurahan').classList.add('d-none');
            const data = response.data.data;
            const sel = document.getElementById('ax-kelurahan');
            sel.disabled = false;
            data.forEach(function (item) {
                sel.appendChild(new Option(item.nama, item.id));
            });
        })
        .catch(function (error) {
            document.getElementById('ax-load-kelurahan').classList.add('d-none');
            console.log('Gagal ambil kelurahan (Axios):', error);
        });
    });

    // ── Kelurahan ──────────────────────────────────────────────────
    document.getElementById('ax-kelurahan').addEventListener('change', function () {
        const resultBox = document.getElementById('ax-result-box');
        if (!this.value) { resultBox.style.display = 'none'; return; }

        const prov = document.getElementById('ax-provinsi').options[document.getElementById('ax-provinsi').selectedIndex].text;
        const kota = document.getElementById('ax-kota').options[document.getElementById('ax-kota').selectedIndex].text;
        const kec  = document.getElementById('ax-kecamatan').options[document.getElementById('ax-kecamatan').selectedIndex].text;
        const kel  = this.options[this.selectedIndex].text;

        document.getElementById('ax-result').textContent = `${kel}, Kec. ${kec}, ${kota}, ${prov}`;
        resultBox.style.display = 'block';
    });

})();
</script>
@endpush