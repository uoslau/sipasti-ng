<form method="POST" action="{{ route('kegiatan.store') }}">
    @csrf
    {{-- nama kegiatan --}}
    <div class="row">
        <div class="col-md-12">
            <div class="mb-6">
                <label class="form-label" for="nama_kegiatan">Nama Kegiatan</label>
                <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror" id="nama_kegiatan"
                    name="nama_kegiatan" placeholder="Gunakan Huruf Kapital Untuk Setiap Awal Kata" required autofocus
                    value="{{ old('nama_kegiatan') }}" />
                @error('nama_kegiatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    {{-- tanggal mulai selesai --}}
    <div class="row">
        <div class="col-md-6">
            <div class="mb-6">
                <label for="tangal_mulai" class="form-label">Tanggal Mulai</label>
                <input class="form-control" type="date" value="{{ old('tanggal_mulai') }}" id="tanggal_mulai"
                    name="tanggal_mulai" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-6">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input class="form-control" type="date" value="{{ old('tanggal_selesai') }}" id="tanggal_selesai"
                    name="tanggal_selesai" />
            </div>
        </div>
    </div>
    {{-- mata anggaran, fungsi --}}
    <div class="row">
        {{-- <div class="col-md-6">
            <div class="mb-6">
                <label for="mata_anggaran_id" class="form-label">Mata Anggaran</label>
                <select class="form-select" id="mata_anggaran_id" name="mata_anggaran_id">
                    <option selected disabled>Pilih Mata Anggaran</option>
                    @foreach ($mataanggaran as $m)
                        @if (old('mata_anggaran_id') == $m->id)
                            <option value="{{ $m->id }}" selected>{{ $m->mata_anggaran }}
                            </option>
                        @else
                            <option value="{{ $m->id }}">{{ $m->mata_anggaran }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="col-md-6">
            <div class="mb-6">
                <label class="form-label" for="mata_anggaran">Beban Anggaran</label>
                <input type="text" class="form-control @error('mata_anggaran') is-invalid @enderror"
                    id="mata_anggaran" name="mata_anggaran" placeholder="contoh: 2903.BMA.009.005.A.521213" required
                    autofocus value="{{ old('mata_anggaran') }}" />
                @error('mata_anggaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        {{-- Fungsi --}}
        {{-- <div class="col-md-6">
            <div class="mb-6">
                <label for="fungsi_id" class="form-label">Fungsi</label>
                <select class="form-select" id="fungsi_id" name="fungsi_id">
                    <option selected disabled>Pilih Fungsi</option>
                    @foreach ($fungsi as $f)
                        @if (old('fungsi_id') == $f->id)
                            <option value="{{ $f->id }}" selected>
                                {{ $f->fungsi }}
                            </option>
                        @else
                            <option value="{{ $f->id }}">{{ $f->fungsi }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="col-md-6">
            <div class="mb-6">
                <label for="tim_kerja_id" class="form-label">Tim Kerja</label>
                <select class="form-select" id="tim_kerja_id" name="tim_kerja_id">
                    <option selected disabled>Pilih Tim Kerja</option>
                    @foreach ($tim_kerja as $tk)
                        @if ($tk->id !== 12)
                            @if (old('tim_kerja_id') == $tk->id)
                                <option value="{{ $tk->id }}" selected>
                                    {{ $tk->tim_kerja }}
                                </option>
                            @else
                                <option value="{{ $tk->id }}">
                                    {{ $tk->tim_kerja }}
                                </option>
                            @endif
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    {{-- honor nias, nias barat --}}
    <div class="row">
        <div class="col-md-6">
            <div class="mb-6">
                <label for="honor_nias" class="form-label">Honor Nias</label>
                <input class="form-control" type="text" value="{{ old('honor_nias') }}" id="honor_nias"
                    name="honor_nias" placeholder="per satuan" oninput="formatRupiah(this)" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-6">
                <label for="honor_nias_barat" class="form-label">Honor Nias Barat</label>
                <input class="form-control" type="text" value="{{ old('honor_nias_barat') }}" id="honor_nias_barat"
                    name="honor_nias_barat" placeholder="per satuan" oninput="formatRupiah(this)" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Tambah</button>
    </div>
</form>

<script>
    function formatRupiah(input) {
        let value = input.value.replace(/\./g, '');
        if (!isNaN(value)) {
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } else {
            input.value = value.slice(0, -1);
        }
    }
</script>
