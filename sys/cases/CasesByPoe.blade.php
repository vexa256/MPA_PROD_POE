<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-md-12">
        <!--begin::Card-->
        <div class="card card-flush h-xl-100">
            <!--begin::Card header-->
            <div class="card-header pt-7">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Suspected Cases by Point of Entry (POE)</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Overview of suspected disease cases across points
                        of entry</span>
                </h3>
                <!--end::Title-->
                <!--begin::Toolbar-->
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-duotone ki-category fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </button>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold w-200px"
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
                    <!--end::Menu-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-7">
                <!--begin::Filters-->
                <form action="{{ route('getSuspectedCasesByPoe') }}" method="GET" class="mb-8">
                    <div class="row g-5">
                        <div class="col-md-3">
                            <label class="form-label fs-6 fw-semibold text-gray-700">Start Date</label>
                            <div class="position-relative d-flex align-items-center">
                                <i class="ki-duotone ki-calendar-8 fs-2 position-absolute ms-4"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span><span
                                        class="path5"></span><span class="path6"></span></i>
                                <input type="date" name="start_date" class="form-control form-control-solid ps-12"
                                    placeholder="Pick date" value="{{ request('start_date') }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-6 fw-semibold text-gray-700">End Date</label>
                            <div class="position-relative d-flex align-items-center">
                                <i class="ki-duotone ki-calendar-8 fs-2 position-absolute ms-4"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span><span
                                        class="path5"></span><span class="path6"></span></i>
                                <input type="date" name="end_date" class="form-control form-control-solid ps-12"
                                    placeholder="Pick date" value="{{ request('end_date') }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-6 fw-semibold text-gray-700">Point of Entry</label>
                            <select name="poe_name" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select Point of Entry">
                                <option value="">All Points of Entry</option>
                                @foreach ($suspectedCasesByPoe->unique('poe_name') as $caseData)
                                    <option value="{{ $caseData->poe_name ?? '' }}"
                                        {{ request('poe_name') == ($caseData->poe_name ?? '') ? 'selected' : '' }}>
                                        {{ $caseData->poe_name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-6 fw-semibold text-gray-700">Month</label>
                            <input type="number" name="month" class="form-control form-control-solid"
                                placeholder="e.g. 1 for January" value="{{ request('month') }}" />
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-7">
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Apply Filter</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
                <!--end::Filters-->

                <!--begin::Stats summary-->
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="min-w-150px rounded-start">Point of Entry</th>
                                <th class="min-w-150px">Suspected Disease</th>
                                <th class="min-w-100px rounded-end">Suspected Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suspectedCasesByPoe as $caseData)
                                <tr>
                                    <td class="fw-semibold text-gray-800">{{ $caseData->poe_name ?? 'N/A' }}</td>
                                    <td>{{ $caseData->suspected_disease ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge badge-light-primary fs-7 fw-bold">{{ $caseData->suspected_count ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end::Stats summary-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
</div>
