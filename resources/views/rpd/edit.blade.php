<form method="POST" action="" id="editForm">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="mb-6">
                <label class="form-label" for="rpd_kegiatan">Kegiatan</label>
                <input type="text" class="form-control @error('rpd_kegiatan') is-invalid @enderror" id="edit_kegiatan"
                    name="rpd_kegiatan" placeholder="contoh: Dukman, PPIS" required autofocus
                    value="{{ old('rpd_kegiatan') }}" />
                @error('rpd_kegiatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-6">
                <label class="form-label" for="rpd_jenis_belanja">Jenis Belanja</label>
                <input type="text" class="form-control @error('rpd_jenis_belanja') is-invalid @enderror"
                    id="edit_jenis_belanja" name="rpd_jenis_belanja" placeholder="contoh: Belanja Barang" required
                    autofocus value="{{ old('rpd_jenis_belanja') }}" />
                @error('rpd_jenis_belanja')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-6">
                <label class="form-label" for="rpd_output">Output</label>
                <input type="text" class="form-control @error('rpd_output') is-invalid @enderror" id="edit_output"
                    name="rpd_output" placeholder="contoh: GG.2899" required autofocus
                    value="{{ old('rpd_output') }}" />
                @error('rpd_output')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-6">
                <label for="rpd_target" class="form-label">Target</label>
                <input class="form-control" type="text" value="{{ old('rpd_target') }}" id="edit_target"
                    name="rpd_target" placeholder="contoh: 123.456.789" oninput="formatRupiah(this)" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-6">
                <label for="rpd_realisasi" class="form-label">Realisasi</label>
                <input class="form-control" type="text" value="{{ old('rpd_realisasi') }}" id="edit_realisasi"
                    name="rpd_realisasi" placeholder="contoh: 123.456.789" oninput="formatRupiah(this)" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-6">
                <label class="form-label" for="rpd_pic">PIC</label>
                <input type="text" class="form-control @error('rpd_pic') is-invalid @enderror" id="edit_pic"
                    name="rpd_pic" placeholder="contoh: Epianus Zega" required autofocus value="{{ old('rpd_pic') }}" />
                @error('rpd_pic')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-6">
                <label for="rpd_bulan" class="form-label">Periode</label>
                <select class="form-select" id="edit_rpd_bulan" name="rpd_bulan">
                    <option selected disabled>Pilih Periode</option>
                    @foreach ($nama_bulan as $key => $bulan)
                        <option value="{{ $key }}" {{ $key == $bulan_sekarang ? 'selected' : '' }}>
                            {{ $bulan }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <input type="hidden" name="rpd_tahun" value="{{ date('Y') }}">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-6">
                <label for="rpd_catatan" class="form-label">Catatan</label>
                <textarea class="form-control @error('rpd_catatan') is-invalid @enderror" id="edit_catatan" name="rpd_catatan"
                    rows="3">{{ old('rpd_catatan') }}</textarea>
                @error('rpd_catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Edit</button>
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
