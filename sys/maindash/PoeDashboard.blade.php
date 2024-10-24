<div class="row">
    <div class="col-md-4">
        <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
            <!--begin::Card header-->
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Registered POEs</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Overview of entry points</span>
                </h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-category fs-6"><span class="path1"></span><span
                                class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px"
                        data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Quick Actions</div>
                        </div>
                        <div class="separator mb-3 opacity-75"></div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3">View Details</a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3">Export Data</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-7">
                <!--begin::Stats summary-->
                <div class="d-flex flex-wrap justify-content-between mb-5">
                    <div class="d-flex flex-column align-items-center me-5 mb-2">
                        <span class="fs-3 fw-bold text-gray-800 mb-1">{{ $totalAirports }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">Airports</span>
                        <span class="badge badge-light-primary fs-8 fw-bold mt-1">
                            <i class="ki-duotone ki-airplane-square fs-9 me-1"><span class="path1"></span><span
                                    class="path2"></span></i>Air
                        </span>
                    </div>
                    <div class="d-flex flex-column align-items-center me-5 mb-2">
                        <span class="fs-3 fw-bold text-gray-800 mb-1">{{ $totalLandBorders }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">Land Borders</span>
                        <span class="badge badge-light-success fs-8 fw-bold mt-1">
                            <i class="ki-duotone ki-truck fs-9 me-1"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span><span
                                    class="path4"></span></i>Land
                        </span>
                    </div>
                    <div class="d-flex flex-column align-items-center mb-2">
                        <span class="fs-3 fw-bold text-gray-800 mb-1">{{ $totalSeaports }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">Seaports</span>
                        <span class="badge badge-light-info fs-8 fw-bold mt-1">
                            <i class="ki-duotone ki-ship fs-9 me-1"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span></i>Sea
                        </span>
                    </div>
                </div>
                <!--end::Stats summary-->

                <!--begin::Chart-->
                <canvas id="poeChart" width="400" height="400"></canvas>
                <!--end::Chart-->
            </div>
            <!--end::Card body-->
        </div>
    </div>


    {{-- province chart --}}

    <div class="col-md-8">
        <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
            <!--begin::Card header-->
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Geographical Distribution of POEs by Province</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Overview of entry points by province</span>
                </h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-category fs-6"><span class="path1"></span><span
                                class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px"
                        data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Quick Actions</div>
                        </div>
                        <div class="separator mb-3 opacity-75"></div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3">View Details</a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3">Export Data</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-7">
                <!--begin::Stats summary-->
                <div class="d-flex flex-wrap justify-content-start mb-5">
                    <div class="d-flex flex-column align-items-start me-5 mb-2">
                        <span class="fs-3 fw-bold text-gray-800 mb-1">{{ $provinceDistribution->count() }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">Provinces with POEs</span>
                        <span class="badge badge-light-warning fs-8 fw-bold mt-1">
                            <i class="ki-duotone ki-globe fs-9 me-1"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span></i>Regions
                        </span>
                    </div>
                    <div class="d-flex flex-column align-items-start mb-2">
                        <span class="fs-3 fw-bold text-gray-800 mb-1">{{ $provinceDistribution->sum('total') }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">Total POEs across Provinces</span>
                        <span class="badge badge-light-primary fs-8 fw-bold mt-1">
                            <i class="ki-duotone ki-map-pin fs-9 me-1"><span class="path1"></span><span
                                    class="path2"></span></i>Locations
                        </span>
                    </div>
                </div>
                <!--end::Stats summary-->

                <!--begin::Chart-->
                <canvas id="provinceChart" width="400" height="200"></canvas>
                <!--end::Chart-->
            </div>
            <!--end::Card body-->
        </div>
    </div>

    <div class="col-md-12 mt-3">
        <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
            <!--begin::Card header-->
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">User Roles Distribution by POE</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Focusing on Admin, Screener
                        roles</span>
                </h3>

            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-7">
                <div class="table-responsive">
                    <table class="table mytable table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Point of Entry</th>
                                <th class="min-w-100px">District</th>
                                <th class="min-w-100px">Province</th>
                                <th class="min-w-140px">Role</th>
                                <th class="min-w-120px">Total</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userRolesByPOE as $roleData)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ asset('images/user.svg') }}" alt="" />
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="#"
                                                    class="text-dark fw-bold text-hover-primary fs-6">{{ $roleData->poeName }}</a>
                                                <span
                                                    class="text-muted fw-semibold text-muted d-block fs-7">{{ $roleData->poeType }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $roleData->district ?? 'N/A' }}</td>
                                    <td>{{ $roleData->province ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge badge-light-{{ $roleData->role === 'admin' ? 'danger' : 'success' }} fw-bold">{{ ucfirst($roleData->role) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold d-block fs-6">{{ $roleData->total }}</span>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Card body-->
        </div>
    </div>

    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}



    <!-- Geographical Distribution of Users by Province Table -->
    <div class="col-md-12 mt-5">
        <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
            <!--begin::Card header-->
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Geographical Distribution of Users by Province</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Overview of users across different provinces</span>
                </h3>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-7">
                <div class="table-responsive">
                    <table class="table mytable table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Province</th>
                                <th class="min-w-120px">Total Users</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($totalUsersByProvince as $provinceData)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ asset('images/user.svg') }}" alt="" />
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="#"
                                                    class="text-dark fw-bold text-hover-primary fs-6">{{ $provinceData->province ?? 'N/A' }}</a>
                                                <span
                                                    class="text-muted fw-semibold text-muted d-block fs-7">Province</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold d-block fs-6">{{ $provinceData->total }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Card body-->
        </div>
    </div>
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    <div class="col-md-12 mt-5">
        <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
            <!--begin::Card header-->
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Geographical Distribution of Users by District</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Overview of users across different districts</span>
                </h3>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-7">
                <div class="table-responsive">
                    <table class="table mytable table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">District</th>
                                <th class="min-w-120px">Total Users</th>
                                <th class="min-w-100px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                            @endphp
                            @foreach ($districtDistribution as $districtData)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-45px me-5">
                                                <img src="{{ asset('images/user.svg') }}" alt="" />
                                            </div>
                                            <div class="d-flex justify-content-start flex-column">
                                                <a href="#"
                                                    class="text-{{ $colors[array_rand($colors)] }} fw-bold text-hover-primary fs-6">{{ $districtData->district ?? 'N/A' }}</a>
                                                <span
                                                    class="text-muted fw-semibold text-muted d-block fs-7">District</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold d-block fs-6">{{ $districtData->total }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $status = $districtData->total > 50 ? 'Active' : 'Active';
                                            $statusColor = $status === 'Active' ? 'success' : 'danger';
                                            $statusIcon = $status === 'Active' ? 'check-circle' : 'x-circle';
                                        @endphp
                                        <span class="badge badge-light-{{ $statusColor }} fw-bold">
                                            <i class="fas fa-{{ $statusIcon }} me-2"></i>
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Card body-->
        </div>
    </div>

    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}
    {{-- user analysis --}}

</div>
