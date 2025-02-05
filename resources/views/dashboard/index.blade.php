<x-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <form method="GET" action="{{ route('dashboard.index') }}">
                @csrf
                <div class="card-header d-flex align-items-center">
                    <h5 class="mb-0 me-2">Daftar Mitra Bulan </h5>
                    <select id="bulan" name="bulan" class="form-select w-auto" onchange="this.form.submit()">
                        @foreach ($nama_bulan as $key => $bulan)
                            <option value="{{ $key }}" {{ $key == $bulan_sekarang ? 'selected' : '' }}>
                                {{ $bulan }}
                            </option>
                        @endforeach
                    </select>
                    <select id="tahun" name="tahun" class="form-select w-auto" onchange="this.form.submit()">
                        @foreach ($tahun_range as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == $tahun_sekarang ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('kontrak.download', $slug) }}" id="downloadButton" class="btn btn-primary ms-auto"
                        style="margin-right: 20px"><i class="bx bx-download me-1"></i> Download Kontrak</a>
                </div>
            </form>
            @if ($petugas_bulan->isEmpty())
                <h5 class="card-header border-top text-center">Belum ada mitra pada bulan ini. Silahkan menambah
                    kegiatan di menu "Kegiatan".</h5>
            @else
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-center">Honor Bulan Ini</th>
                                <th class="text-center">Sisa Bulan Ini</th>
                                <th class="text-center">Bisa Dibayar</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom">
                            @foreach ($petugas_bulan as $p)
                                <tr class="clickable-row" data-target="#details-{{ $loop->index }}"
                                    data-toggle="collapse">
                                    <td
                                        style="max-width: 500px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ ucwords(strtolower($p['nama_mitra'])) }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-label-primary">Rp.
                                            {{ number_format($p['total_honor'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-label-danger">
                                            @if ($p['honor_max'] - $p['total_honor'] <= 0)
                                                Rp. 0
                                            @else
                                                Rp.
                                                {{ number_format($p['honor_max'] - $p['total_honor'], 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-label-primary">
                                            @if ($p['total_honor'] > $p['honor_max'])
                                                Rp. {{ number_format($p['honor_max'], 0, ',', '.') }}
                                            @else
                                                Rp. {{ number_format($p['total_honor'], 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center p-0">
                                        <i class="menu-icon tf-icons bx bx-chevron-down toggle-arrow"></i>
                                    </td>
                                </tr>
                                <tr id="details-{{ $loop->index }}" class="collapse">
                                    <td colspan="5">
                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-borderless table-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="py-0" colspan="2">Kegiatan yang diikuti</th>
                                                        <th class="text-center py-0">Mulai</th>
                                                        <th class="text-center py-0">Selesai</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    @foreach ($p['kegiatan'] as $k)
                                                        <tr>
                                                            <td class="text-center px-0" style="width: 15ch;">
                                                                <span
                                                                    class="badge rounded-pill {{ $k['status'] === 'Belum Mulai' ? 'bg-label-warning' : ($k['status'] === 'Sedang Berjalan' ? 'bg-label-primary' : 'bg-label-success') }}">
                                                                    {{ $k['status'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-left px-0"
                                                                style="max-width: 60ch; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                {{ $k['nama_kegiatan'] }}
                                                            </td>
                                                            <td class="text-center p-0" style="width: 20ch;">
                                                                <span class="badge rounded-pill bg-label-info">
                                                                    {{ $k['tanggal_mulai'] }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center p-0" style="width: 20ch;">
                                                                <span class="badge rounded-pill bg-label-info">
                                                                    {{ $k['tanggal_selesai'] }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.clickable-row');
        const checkboxes = document.querySelectorAll('.form-check-input');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        });

        rows.forEach(row => {
            row.addEventListener('click', function() {
                const targetId = row.getAttribute('data-target');
                const targetRow = document.querySelector(targetId);
                const arrow = row.querySelector('.toggle-arrow');

                if (targetRow.classList.contains('show')) {
                    targetRow.classList.remove('show');
                    arrow.style.transform = 'rotate(0deg)';
                    row.classList.remove('active-row');
                } else {
                    targetRow.classList.add('show');
                    arrow.style.transform = 'rotate(180deg)';
                    row.classList.add('active-row');
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterNama = document.getElementById('filter-nama');
        const tableBody = document.querySelector('tbody');

        function applyFilter() {
            const namaValue = filterNama.value.toLowerCase();
            const rows = tableBody.querySelectorAll('.clickable-row');

            rows.forEach(row => {
                const nama = row.querySelector('td:nth-child(1)').innerText.toLowerCase();
                const matchNama = nama.includes(namaValue);

                if (matchNama) {
                    row.style.display = '';
                    const targetId = row.getAttribute('data-target');
                    const detailRow = document.querySelector(targetId);
                    if (detailRow) detailRow.style.display = '';
                } else {
                    row.style.display = 'none';
                    const targetId = row.getAttribute('data-target');
                    const detailRow = document.querySelector(targetId);
                    if (detailRow) detailRow.style.display = 'none';
                }
            });
        }

        filterNama.addEventListener('input', applyFilter);
    });
</script>

<style>
    .collapse {
        display: none;
    }

    .collapse.show {
        display: table-row;
    }

    .toggle-arrow {
        transition: transform 0.3s ease;
    }

    .active-row {
        background-color: #f5f5f5;
        transition: background-color 0.3s ease;
    }
</style>
