<x-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0 me-2">Tabel Indikator Rencana Penarikan Dana April 2025</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Output</th>
                            <th>Target</th>
                            <th>Realisasi SP2D</th>
                            <th>Selisih</th>
                            <th>Deviasi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom">
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
