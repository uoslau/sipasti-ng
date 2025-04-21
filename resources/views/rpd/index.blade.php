<x-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <form method="GET" action="{{ route('rpd.index') }}">
                @csrf
                <div class="card-header d-flex align-items-center">
                    <h5 class="mb-0 me-2">Tabel Indikator Rencana Penarikan Dana</h5>
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
                    <div class="mx-1 row w-auto">
                        <div class="col-md-10">
                            <input class="form-control" type="search" value="Cari PIC ..." id="filter-nama" />
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addModal"
                        style="margin-right: 20px;">
                        <i class='bx bxs-file-plus'></i> Data
                    </a>
                </div>
            </form>
            <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel3">
                                Tambah Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('rpd.create')
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel3">
                                Edit Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @include('rpd.edit')
                        </div>
                    </div>
                </div>
            </div>
            @if ($list_rpd->isEmpty())
                <h5 class="card-header border-top text-center">Belum ada data.</h5>
            @else
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kegiatan / Jenis Belanja / Output</th>
                                <th class="text-center">Target</th>
                                <th class="text-center">Realisasi SP2D</th>
                                <th class="text-center">Selisih</th>
                                <th class="text-center">Deviasi</th>
                                <th class="text-center">PIC</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom">
                            @foreach ($list_rpd as $r)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            {{ $r->kegiatan }}
                                        </span>
                                        /
                                        <span class="badge bg-label-primary">
                                            {{ $r->jenis_belanja }}
                                        </span>
                                        /
                                        <span class="badge bg-label-primary">
                                            {{ $r->output }}
                                        </span>
                                        @if (empty($r->catatan))
                                        @else
                                            /
                                            <a class="badge badge-center rounded-pill bg-label-warning"
                                                data-bs-toggle="collapse" href="#collapse-{{ $loop->index }}"
                                                aria-controls="collapse-{{ $loop->index }}">
                                                <i class='bx bx-note'></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-label-dark">
                                            Rp. {{ number_format($r->target, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-label-dark">
                                            Rp. {{ number_format($r->realisasi, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-label-dark">
                                            Rp. {{ number_format($r->selisih, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge {{ $r->warna }}">
                                            @if ($r->target != 0)
                                                {{ number_format($r->deviasi, 2, ',', '.') }} %
                                            @else
                                                0 %
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-primary">
                                            {{ $r->pic }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <li>
                                                    <a href="#" class="dropdown-item edit-rpd-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-id="{{ $r->id }}"
                                                        data-kegiatan="{{ $r->kegiatan }}"
                                                        data-jenis-belanja="{{ $r->jenis_belanja }}"
                                                        data-output="{{ $r->output }}"
                                                        data-target="{{ $r->target }}"
                                                        data-realisasi="{{ $r->realisasi }}"
                                                        data-pic="{{ $r->pic }}"
                                                        data-bulan="{{ $r->bulan }}"
                                                        data-catatan="{{ $r->catatan }}">
                                                        <i class='bx bxs-edit'></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"><i
                                                            class='bx bxs-trash'></i>Delete</a>
                                                </li>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @if (!empty($r->catatan))
                                    <tr class="collapse" id="collapse-{{ $loop->index }}">
                                        <td colspan="100%">
                                            <div class="d-flex align-items-start gap-2"
                                                style="white-space: pre-wrap;">
                                                <i class='bx bx-subdirectory-right'></i><span
                                                    class="badge bg-label-warning">Catatan :
                                                </span>{{ $r->catatan }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @foreach ($rekap_jenis_belanja as $rb)
                                <tr>
                                    <td class=" text-center fw-bold">Total {{ $rb['jenis_belanja'] }}</td>
                                    <td class="text-end fw-bold">
                                        <span class="badge bg-primary">
                                            Rp. {{ number_format($rb['total_target'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <span class="badge bg-primary">
                                            Rp. {{ number_format($rb['total_realisasi'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <span class="badge bg-primary">
                                            Rp.
                                            {{ number_format($rb['selisih'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center" colspan="2">
                                        <span class="badge {{ $rb['warna'] }}">
                                            @if ($rb['total_target'] != 0)
                                                {{ number_format($rb['deviasi'], 2, ',', '.') }} %
                                            @else
                                                0 %
                                            @endif
                                        </span>
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class=" text-center fw-bold">Total Belanja</td>
                                <td class="text-end fw-bold">
                                    <span class="badge bg-primary">
                                        Rp. {{ number_format($rekap_total->total_target, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold">
                                    <span class="badge bg-primary">
                                        Rp. {{ number_format($rekap_total->total_realisasi, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold">
                                    <span class="badge bg-primary">
                                        Rp. {{ number_format($rekap_total->selisih, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold" colspan="2">
                                    <span class="badge {{ $rekap_total->warna }}"">
                                        @if ($rekap_total->total_target != 0)
                                            {{ number_format($rekap_total->deviasi, 2, ',', '.') }} %
                                        @else
                                            0 %
                                        @endif
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-rpd-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;

                const form = document.getElementById('editForm');
                form.action = `/monitoring_rpd/${id}`;

                document.getElementById('edit_kegiatan').value = this.dataset.kegiatan;
                document.getElementById('edit_jenis_belanja').value = this.dataset.jenisBelanja;
                document.getElementById('edit_output').value = this.dataset.output;
                document.getElementById('edit_target').value = this.dataset.target;
                formatRupiah(document.getElementById('edit_target'));
                document.getElementById('edit_realisasi').value = this.dataset.realisasi;
                formatRupiah(document.getElementById('edit_realisasi'));
                document.getElementById('edit_pic').value = this.dataset.pic;
                document.getElementById('edit_bulan').value = this.dataset.bulan;
                document.getElementById('edit_catatan').value = this.dataset.catatan;

                document.getElementById('editForm').action = `/rpd/${this.dataset.id}`;
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
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const nama = row.querySelector('td:nth-child(6)')?.innerText.toLowerCase() || '';
                const matchNama = nama.includes(namaValue);

                row.style.display = matchNama ? '' : 'none';
            });
        }

        filterNama.addEventListener('input', applyFilter);
    });
</script>
