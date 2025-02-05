{{-- @dd($kegiatan) --}}
<x-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Daftar Kegiatan</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"
                    style="margin-right: 20px;"><i class="bx bx-plus-circle me-1"></i>
                    Tambah Kegiatan
                </button>
                <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">
                                    Tambah Kegiatan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('kegiatan.create')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Kegiatan</th>
                            <th class="text-center">Budget</th>
                            <th class="text-center">Tim Kerja</th>
                            <th class="text-center">Mulai</th>
                            <th class="text-center">Selesai</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom">
                        @foreach ($kegiatan as $k)
                            <tr>
                                <td
                                    style="max-width: 60ch; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <a href="{{ route('kegiatan.show', [$k->slug]) }}">{{ $k->nama_kegiatan }}</a>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-primary">Rp.
                                        {{ number_format($k->petugas_kegiatan_sum_honor, 0, '.', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-primary">
                                        @if ($k->tim_kerja_id == 12)
                                            {{ $k->fungsi->fungsi }}
                                        @else
                                            {{ $k->timkerja->tim_kerja_alias }}
                                        @endif
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-info">
                                        {{ $k->tanggal_mulai }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-info">
                                        {{ $k->tanggal_selesai }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('kegiatan.edit', [$k->slug]) }}"><i
                                                    class="bx bx-edit-alt me-1"></i> Edit</a>
                                            <a class="dropdown-item"
                                                href="{{ route('kegiatan.download', [$k->slug]) }}"><i
                                                    class="bx bx-download me-1"></i> Download</a>
                                            <form action="{{ route('kegiatan.destroy', [$k->slug]) }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <button class="dropdown-item"
                                                    onclick="return confirm('Hapus Kegiatan?')"><i
                                                        class="bx bx-trash me-1"></i> Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination pagination-sm justify-content-end pt-3">
                    {{ $kegiatan->onEachSide(0)->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
