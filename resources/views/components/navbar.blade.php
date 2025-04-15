<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="bx bx-menu bx-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        @if (request()->routeIs('dashboard.index'))
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search bx-md"></i>
                    <input type="text" id="filter-nama" class="form-control border-0 shadow-none ps-1 ps-sm-2"
                        placeholder="Ketik untuk mencari" aria-label="Ketik untuk mencari" />
                </div>
            </div>
            {{-- @elseif (request()->routeIs('kegiatan.index'))
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search bx-md"></i>
                    <input type="text" id="filter-kegiatan" class="form-control border-0 shadow-none ps-1 ps-sm-2"
                        placeholder="Cari kegiatan..." aria-label="Cari kegiatan..." />
                </div>
            </div> --}}
        @endif
        <!-- /Search -->
    </div>
</nav>
