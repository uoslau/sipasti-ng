{{-- @dd($petugas) --}}
<x-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Kegiatan</th>
                            <th>Nama Mitra</th>
                            <th class="text-center">Nomor Kontrak</th>
                            <th class="text-center">Nomor BAST</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom">
                        @foreach ($petugas as $p)
                            <tr>
                                <td>
                                    {{ $p->kegiatan->nama_kegiatan }}
                                </td>
                                <td
                                    style="max-width: 500px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ ucwords(strtolower($p->nama_mitra)) }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-danger">
                                        {{ $p->nomor_kontrak }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-primary">
                                        {{ $p->nomor_bast }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination pagination-sm justify-content-end pt-3">
                    {{ $petugas->onEachSide(0)->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
