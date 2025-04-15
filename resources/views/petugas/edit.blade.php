<x-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Petugas</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('petugas.update', [$kegiatan->slug, $petugas->id]) }}">
                            @csrf
                            @method('PUT')
                            {{-- nama petugas --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-6">
                                        <label class="form-label" for="nama_mitra">Nama Mitra</label>
                                        <input type="text"
                                            class="form-control @error('nama_mitra') is-invalid @enderror"
                                            id="nama_mitra" name="nama_mitra" placeholder="Ketik Untuk Mencari" required
                                            autofocus value="{{ old('nama_mitra', $petugas->nama_mitra) }}" disabled
                                            readonly />
                                        @error('nama_mitra')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- bertugas_sebagai, wilayah_tugas --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="bertugas_sebagai">Bertugas Sebagai</label>
                                        <input type="text"
                                            class="form-control @error('bertugas_sebagai') is-invalid @enderror"
                                            id="bertugas_sebagai" name="bertugas_sebagai"
                                            placeholder="Misalnya PCL, PML, Operator Entri, dll" required autofocus
                                            value="{{ old('bertugas_sebagai', $petugas->bertugas_sebagai) }}" />
                                        @error('bertugas_sebagai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-6">
                                        <label for="wilayah_tugas" class="form-label">Wilayah Tugas</label>
                                        <select class="form-select" id="wilayah_tugas" name="wilayah_tugas">
                                            <option selected disabled>Pilih Wilayah Tugas</option>
                                            @foreach ($wilayah_tugas as $w)
                                                @if (old('wilayah_tugas', $petugas->wilayah_tugas) == $w->kode_kabupaten)
                                                    <option value="{{ $w->kode_kabupaten }}" selected>
                                                        {{ $w->nama_kabupaten }}
                                                    </option>
                                                @else
                                                    <option value="{{ $w->kode_kabupaten }}">{{ $w->nama_kabupaten }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- beban --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-6">
                                        <label for="beban" class="form-label">Beban</label>
                                        <input class="form-control" type="number"
                                            value="{{ old('beban', $petugas->beban) }}" id="beban" name="beban"
                                            placeholder="Beban Penugasan" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-6">
                                        <label class="form-label" for="satuan">Satuan</label>
                                        <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                            id="satuan" name="satuan"
                                            placeholder="Misalnya Dokumen, Segmen, Rumah Tangga, dll" required autofocus
                                            value="{{ old('satuan', $petugas->satuan) }}" />
                                        @error('satuan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
