<x-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            {{-- @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif --}}
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0 me-2">Versi Mitra Kepka Saat Ini : </h5>
                <a href="#" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addModal"
                    style="margin-right: 20px;">
                    <i class='bx bxs-edit'></i> Mitra
                </a>
                <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel3">Tambah
                                    Mitra</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @include('mitra.create')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Mitra</th>
                            {{-- <th class="text-center">NIK</th> --}}
                            <th class="text-center">Posisi</th>
                            <th class="text-center">Alamat</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom">
                        @foreach ($mitra as $m)
                            <tr>
                                <td
                                    style="max-width: 60ch; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ ucwords(strtolower($m->nama_mitra)) }}
                                </td>
                                {{-- <td class="text-center">
                                    <span class="badge bg-label-danger">
                                        {{ $m->sktnp }}
                                    </span>
                                </td> --}}
                                <td class="text-center">
                                    <span class="badge bg-label-primary">
                                        {{ $m->posisi }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $m->alamat }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination pagination-sm justify-content-end pt-3">
                    {{ $mitra->onEachSide(0)->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
