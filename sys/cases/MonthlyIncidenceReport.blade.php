<div class="d-flex flex-column-fluid">
    <div class="container-xxl">
        <div class="row g-5 g-xl-8">
            <div class="col-xl-3">
                <!--begin::Sidebar-->
                <div class="card card-flush mb-5 mb-xl-8">
                    <div class="card-header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Filters</span>
                        </h3>
                    </div>
                    <div class="card-body py-5">
                        <form action="{{ route('CasesReport') }}" method="GET">
                            <div class="mb-5">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control form-control-solid" id="start_date"
                                    name="start_date" value="{{ $filters['start_date'] ?? '' }}">
                            </div>
                            <div class="mb-5">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control form-control-solid" id="end_date"
                                    name="end_date" value="{{ $filters['end_date'] ?? '' }}">
                            </div>
                            <div class="mb-5">
                                <label for="poe_id" class="form-label">Point of Entry</label>
                                <select class="form-select form-select-solid" id="poe_id" name="poe_id"
                                    data-control="select2" data-placeholder="Select POE">
                                    <option value="">All</option>
                                    @foreach ($pointsOfEntry as $poe)
                                        <option value="{{ $poe->id }}"
                                            {{ $filters['poe_id'] == $poe->id ? 'selected' : '' }}>
                                            {{ $poe->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-5">
                                <label for="province" class="form-label">Province</label>
                                <select class="form-select form-select-solid" id="province" name="province"
                                    data-control="select2" data-placeholder="Select Province">
                                    <option value="">All</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province }}"
                                            {{ $filters['province'] == $province ? 'selected' : '' }}>
                                            {{ $province }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-5">
                                <label for="district" class="form-label">District</label>
                                <select class="form-select form-select-solid" id="district" name="district"
                                    data-control="select2" data-placeholder="Select District">
                                    <option value="">All</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district }}"
                                            {{ $filters['district'] == $district ? 'selected' : '' }}>
                                            {{ $district }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::Sidebar-->
            </div>
            <div class="col-xl-9">
                <!--begin::Statistics-->
                <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                    <div class="col-xl-4">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100"
                            style="background-color: #F1416C;background-image:url('assets/media/svg/shapes/wave-bg-red.svg')">
                            <div class="card-header pt-5 mb-3">
                                <div class="d-flex flex-center rounded-circle h-80px w-80px"
                                    style="border: 1px dashed rgba(255, 255, 255, 0.4);background-color: #F1416C">
                                    <span class="svg-icon svg-icon-3x svg-icon-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M21 10H13V11C13 11.6 12.6 12 12 12C11.4 12 11 11.6 11 11V10H3C2.4 10 2 10.4 2 11V13H22V11C22 10.4 21.6 10 21 10Z"
                                                fill="white" />
                                            <path opacity="0.3"
                                                d="M12 12C11.4 12 11 11.6 11 11V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V11C13 11.6 12.6 12 12 12Z"
                                                fill="white" />
                                            <path opacity="0.3"
                                                d="M18.1 21H5.9C5.4 21 4.9 20.6 4.8 20.1L3 13H21L19.2 20.1C19.1 20.6 18.6 21 18.1 21ZM13 18V15C13 14.4 12.6 14 12 14C11.4 14 11 14.4 11 15V18C11 18.6 11.4 19 12 19C12.6 19 13 18.6 13 18ZM17 18V15C17 14.4 16.6 14 16 14C15.4 14 15 14.4 15 15V18C15 18.6 15.4 19 16 19C16.6 19 17 18.6 17 18ZM9 18V15C9 14.4 8.6 14 8 14C7.4 14 7 14.4 7 15V18C7 18.6 7.4 19 8 19C8.6 19 9 18.6 9 18Z"
                                                fill="white" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex flex-column flex-grow-1">
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="text-white fw-bold fs-1 mb-2">{{ $totalCases }}</span>
                                        <span class="text-white fw-bold fs-6">Total Suspected Cases</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100"
                            style="background-color: #7239EA;background-image:url('assets/media/svg/shapes/wave-bg-purple.svg')">
                            <div class="card-header pt-5 mb-3">
                                <div class="d-flex flex-center rounded-circle h-80px w-80px"
                                    style="border: 1px dashed rgba(255, 255, 255, 0.4);background-color: #7239EA">
                                    <span class="svg-icon svg-icon-3x svg-icon-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M21 10H13V11C13 11.6 12.6 12 12 12C11.4 12 11 11.6 11 11V10H3C2.4 10 2 10.4 2 11V13H22V11C22 10.4 21.6 10 21 10Z"
                                                fill="white" />
                                            <path opacity="0.3"
                                                d="M12 12C11.4 12 11 11.6 11 11V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V11C13 11.6 12.6 12 12 12Z"
                                                fill="white" />
                                            <path opacity="0.3"
                                                d="M18.1 21H5.9C5.4 21 4.9 20.6 4.8 20.1L3 13H21L19.2 20.1C19.1 20.6 18.6 21 18.1 21ZM13 18V15C13 14.4 12.6 14 12 14C11.4 14 11 14.4 11 15V18C11 18.6 11.4 19 12 19C12.6 19 13 18.6 13 18ZM17 18V15C17 14.4 16.6 14 16 14C15.4 14 15 14.4 15 15V18C15 18.6 15.4 19 16 19C16.6 19 17 18.6 17 18ZM9 18V15C9 14.4 8.6 14 8 14C7.4 14 7 14.4 7 15V18C7 18.6 7.4 19 8 19C8.6 19 9 18.6 9 18Z"
                                                fill="white" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex flex-column flex-grow-1">
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="text-white fw-bold fs-1 mb-2">{{ count($diseaseCounts) }}</span>
                                        <span class="text-white fw-bold fs-6">Unique Suspected Diseases</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100"
                            style="background-color: #50CD89;background-image:url('assets/media/svg/shapes/wave-bg-green.svg')">
                            <div class="card-header pt-5 mb-3">
                                <div class="d-flex flex-center rounded-circle h-80px w-80px"
                                    style="border: 1px dashed rgba(255, 255, 255, 0.4);background-color: #50CD89">
                                    <span class="svg-icon svg-icon-3x svg-icon-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M21 10H13V11C13 11.6 12.6 12 12 12C11.4 12 11 11.6 11 11V10H3C2.4 10 2 10.4 2 11V13H22V11C22 10.4 21.6 10 21 10Z"
                                                fill="white" />
                                            <path opacity="0.3"
                                                d="M12 12C11.4 12 11 11.6 11 11V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V11C13 11.6 12.6 12 12 12Z"
                                                fill="white" />
                                            <path opacity="0.3"
                                                d="M18.1 21H5.9C5.4 21 4.9 20.6 4.8 20.1L3 13H21L19.2 20.1C19.1 20.6 18.6 21 18.1 21ZM13 18V15C13 14.4 12.6 14 12 14C11.4 14 11 14.4 11 15V18C11 18.6 11.4 19 12 19C12.6 19 13 18.6 13 18ZM17 18V15C17 14.4 16.6 14 16 14C15.4 14 15 14.4 15 15V18C15 18.6 15.4 19 16 19C16.6 19 17 18.6 17 18ZM9 18V15C9 14.4 8.6 14 8 14C7.4 14 7 14.4 7 15V18C7 18.6 7.4 19 8 19C8.6 19 9 18.6 9 18Z"
                                                fill="white" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex flex-column flex-grow-1">
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="text-white fw-bold fs-1 mb-2">{{ count($pointsOfEntry) }}</span>
                                        <span class="text-white fw-bold fs-6">Active Points of Entry</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Statistics-->

                <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                    <div class="col-xl-6">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Disease Distribution</span>
                                </h3>
                            </div>
                            <div class="card-body pt-6">
                                <canvas id="kt_charts_widget_1_chart" style="height: 350px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Monthly Trend</span>
                                </h3>
                            </div>
                            <div class="card-body pt-6">
                                <canvas id="kt_charts_widget_2_chart" style="height: 350px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


                <!--begin::Table-->
                <div class="card card-flush mb-5 mb-xl-8">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Suspected Cases</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-150px">Screening ID</th>
                                        <th class="min-w-140px">Traveller Name</th>
                                        <th class="min-w-120px">POE</th>
                                        <th class="min-w-100px">Date</th>
                                        <th class="min-w-100px">Classification</th>
                                        <th class="min-w-100px text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suspectedCases as $case)
                                        <tr>
                                            <td>
                                                <span
                                                    class="text-dark fw-bold text-hover-primary d-block fs-6">{{ $case->screening_id }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-dark fw-bold text-hover-primary d-block fs-6">{{ $case->traveller_name }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-dark fw-bold text-hover-primary d-block fs-6">{{ $case->poe_name }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-dark fw-bold text-hover-primary d-block fs-6">{{ \Carbon\Carbon::parse($case->created_at)->format('Y-m-d') }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-light-{{ $case->classification === 'Suspected Case' ? 'danger' : 'warning' }} fw-bold">{{ $case->classification }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end flex-shrink-0">
                                                    <a href="#"
                                                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#kt_modal_case_details_{{ $case->id }}">
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <path opacity="0.3"
                                                                    d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                                    fill="black" />
                                                                <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                                    fill="black" />
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end::Table-->
            </div>
        </div>
    </div>
</div>

@foreach ($suspectedCases as $case)
    <!--begin::Modal-->
    <div class="modal fade" id="kt_modal_case_details_{{ $case->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Case Details</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Screening ID</label>
                            <input type="text" name="screening_id"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $case->screening_id }}" readonly />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Traveller Name</label>
                            <input type="text" name="traveller_name"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $case->traveller_name }}" readonly />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Age</label>
                            <input type="text" name="age" class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $case->age }}" readonly />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Gender</label>
                            <input type="text" name="gender" class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $case->gender }}" readonly />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Point of Entry</label>
                            <input type="text" name="poe_name"
                                class="form-control form-control-solid mb-3 mb-lg-0" value="{{ $case->poe_name }}"
                                readonly />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Classification</label>
                            <input type="text" name="classification"
                                class="form-control form-control-solid mb-3 mb-lg-0"
                                value="{{ $case->classification }}" readonly />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Symptoms</label>
                            <textarea name="symptoms" class="form-control form-control-solid mb-3 mb-lg-0" rows="3" readonly>{{ implode(', ', json_decode($case->symptoms, true)) }}</textarea>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Suspected Diseases</label>
                            <textarea name="suspected_diseases" class="form-control form-control-solid mb-3 mb-lg-0" rows="3" readonly>
                            @foreach (json_decode($case->suspected_diseases, true) as $disease)
{{ $disease['disease'] }}: {{ number_format($disease['score'] * 100, 2) }}%
@endforeach
                        </textarea>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Recommended Action</label>
                            <textarea name="recommended_action" class="form-control form-control-solid mb-3 mb-lg-0" rows="3" readonly>{{ $case->recommended_action }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->
@endforeach
