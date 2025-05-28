<div class="col-xl-12">
    <div class="nav-align-top mb-6">
        <div class="border-top">
            <div class="tab-pane">
                <div class="col-12">
                    <h5 class="card-header text-center">Import File Excel Mitra Kepka</h5>
                    <div class="card-body demo-vertical-spacing demo-only-element">
                        <form action="{{ route('mitra.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- <input type="hidden" name="slug" value="{{ $slug }}"> --}}
                            <div class="input-group">
                                <input type="file" class="form-control" id="file" name="excel_file" />
                                <button class="btn btn-primary" type="submit">Import</button>
                            </div>
                        </form>
                    </div>
                    <div class="text-center">
                        Template import file excel dapat diunduh <a
                            href="{{ route('file.download', 'petugas_import_excel.xlsx') }}">disini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
