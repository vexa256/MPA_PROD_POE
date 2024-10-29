<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-md-12">
        <!--begin::Filters-->
        <div class="card mb-5">
            <div class="card-body">
                <form action="{{ route('getPriorityDiseaseAlerts') }}" method="GET" class="mb-8">
                    <div class="row g-5">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ request('end_date') }}" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Province</label>
                            <select name="province" class="form-select" data-control="select2"
                                data-placeholder="Select province">
                                <option value="">All Provinces</option>
                                @foreach ($suspectedCases->unique('poe_province') as $caseData)
                                    <option value="{{ $caseData->poe_province ?? '' }}"
                                        {{ request('province') == ($caseData->poe_province ?? '') ? 'selected' : '' }}>
                                        {{ $caseData->poe_province ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">District</label>
                            <select name="district" class="form-select" data-control="select2"
                                data-placeholder="Select district">
                                <option value="">All Districts</option>
                                @foreach ($suspectedCases->unique('poe_district') as $caseData)
                                    <option value="{{ $caseData->poe_district ?? '' }}"
                                        {{ request('district') == ($caseData->poe_district ?? '') ? 'selected' : '' }}>
                                        {{ $caseData->poe_district ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="disease" value="{{ $priorityDisease }}">
                    </div>
                    <div class="d-flex justify-content-end mt-7">
                        <button type="submit" class="btn btn-primary">
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!--end::Filters-->

        <!--begin::Card-->
        <div class="card">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Priority Disease Alerts</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Overview of suspected cases for
                        {{ $priorityDisease }}</span>
                </h3>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span
                                    class="path2"></span></i>
                            Filter
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bold">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5">
                                <div class="mb-10">
                                    <label class="form-label fw-semibold">Status:</label>
                                    <div>
                                        <select class="form-select form-select-solid" data-kt-select2="true"
                                            data-placeholder="Select option" data-allow-clear="true">
                                            <option></option>
                                            <option value="1">Approved</option>
                                            <option value="2">Pending</option>
                                            <option value="3">In Process</option>
                                            <option value="4">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="reset"
                                        class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6"
                                        data-kt-menu-dismiss="true">Reset</button>
                                    <button type="submit" class="btn btn-primary fw-semibold px-6"
                                        data-kt-menu-dismiss="true">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Traveller</th>
                            <th class="min-w-125px">Point of Entry</th>
                            <th class="min-w-125px">Arrival Date</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach ($suspectedCases as $case)
                            <tr>
                                <td class="d-flex align-items-center">
                                    <div class="d-flex flex-column">
                                        <a href="#"
                                            class="text-gray-800 text-hover-primary mb-1">{{ $case->traveller_name ?? 'N/A' }}</a>
                                        <span>{{ $case->age ?? 'N/A' }} years, {{ $case->gender ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>{{ $case->poe_name ?? 'N/A' }}</td>
                                <td>{{ $case->arrival_date ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-light btn-active-light-primary btn-sm"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-duotone ki-down fs-5 m-0"></i>
                                    </button>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                        data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_case_details_{{ $loop->index }}">View
                                                Details</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Card-->
    </div>
</div>

<!-- ... (previous code remains unchanged) ... -->

@foreach ($suspectedCases as $case)
    <!--begin::Modal - View Case Details-->
    <div class="modal fade" id="kt_modal_case_details_{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_case_details_header">
                    <h2 class="fw-bold">Case Details for {{ $case->traveller_name }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close"
                        data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_case_details_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_case_details_header"
                        data-kt-scroll-wrappers="#kt_modal_case_details_scroll" data-kt-scroll-offset="300px">
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="text-gray-800 fw-bold fs-6 me-2">Traveller
                                                        Name:</span>
                                                    <span
                                                        class="badge badge-light-success">{{ $case->traveller_name ?? 'N/A' }}</span>
                                                </div>
                                                <div class="fs-7 text-muted">Age: {{ $case->age ?? 'N/A' }}</div>
                                                <div class="fs-7 text-muted">Gender: {{ $case->gender ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="text-gray-800 fw-bold fs-6 me-2">Point of
                                                        Entry:</span>
                                                    <span
                                                        class="badge badge-light-primary">{{ $case->poe_name ?? 'N/A' }}</span>
                                                </div>
                                                <div class="fs-7 text-muted">District:
                                                    {{ $case->poe_district ?? 'N/A' }}</div>
                                                <div class="fs-7 text-muted">Province:
                                                    {{ $case->poe_province ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="text-gray-800 fw-bold fs-6 me-2">Arrival Date:</span>
                                                    <span
                                                        class="badge badge-light-info">{{ $case->arrival_date ?? 'N/A' }}</span>
                                                </div>
                                                <div class="fs-7 text-muted">Departure Country:
                                                    {{ $case->departure_country ?? 'N/A' }}</div>
                                                <div class="fs-7 text-muted">Travel Destination:
                                                    {{ $case->travel_destination ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="text-gray-800 fw-bold fs-6 me-2">Contact Info:</span>
                                                </div>
                                                <div class="fs-7 text-muted">Address: {{ $case->address ?? 'N/A' }}
                                                </div>
                                                <div class="fs-7 text-muted">Phone: {{ $case->phone_number ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Suspected Diseases</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @if (!empty($case->suspected_diseases))
                                            @php
                                                $suspectedDiseases = json_decode($case->suspected_diseases, true);
                                            @endphp
                                            @if ($suspectedDiseases && is_array($suspectedDiseases))
                                                <div class="table-responsive">
                                                    <table
                                                        class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                                        <thead>
                                                            <tr class="fw-bold text-muted">
                                                                <th class="min-w-150px">Disease</th>
                                                                <th class="min-w-100px">Score</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($suspectedDiseases as $disease)
                                                                <tr>
                                                                    <td>{{ $disease['disease'] ?? 'N/A' }}</td>
                                                                    <td>{{ $disease['score'] ?? 'N/A' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <span class="fs-7 text-muted">No suspected diseases recorded.</span>
                                            @endif
                                        @else
                                            <span class="fs-7 text-muted">No suspected diseases recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Symptoms</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @if (!empty($case->symptoms))
                                            @php
                                                $symptoms = json_decode($case->symptoms, true);
                                            @endphp
                                            @if ($symptoms && is_array($symptoms))
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($symptoms as $symptom)
                                                        <span
                                                            class="badge badge-light-warning fs-7 m-1">{{ $symptom }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="fs-7 text-muted">No symptoms recorded.</span>
                                            @endif
                                        @else
                                            <span class="fs-7 text-muted">No symptoms recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Transit Countries</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @if (!empty($case->transit_countries))
                                            @php
                                                $transitCountries = json_decode($case->transit_countries, true);
                                            @endphp
                                            @if ($transitCountries && is_array($transitCountries))
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($transitCountries as $country)
                                                        <span
                                                            class="badge badge-light-primary fs-7 m-1">{{ $country }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="fs-7 text-muted">No transit countries recorded.</span>
                                            @endif
                                        @else
                                            <span class="fs-7 text-muted">No transit countries recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Travel Exposures</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @if (!empty($case->travel_exposures))
                                            @php
                                                $travelExposures = json_decode($case->travel_exposures, true);
                                            @endphp
                                            @if ($travelExposures && is_array($travelExposures))
                                                <div class="table-responsive">
                                                    <table
                                                        class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                                        <thead>
                                                            <tr class="fw-bold text-muted">
                                                                <th class="min-w-150px">Exposure</th>
                                                                <th class="min-w-100px">Value</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($travelExposures as $exposure => $value)
                                                                <tr>
                                                                    <td>{{ ucfirst($exposure) }}</td>
                                                                    <td>{{ $value }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <span class="fs-7 text-muted">No travel exposures recorded.</span>
                                            @endif
                                        @else
                                            <span class="fs-7 text-muted">No travel exposures recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Emergency Contact</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="fs-7 text-muted mb-1">Name:
                                            {{ $case->emergency_contact_name ?? 'N/A' }}</div>
                                        <div class="fs-7 text-muted">Phone:
                                            {{ $case->emergency_contact_phone ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Referral Information</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="fs-7 text-muted mb-1">Status:
                                            {{ $case->referral_status ?? 'N/A' }}</div>
                                        <div class="fs-7 text-muted mb-1">Province:
                                            {{ $case->referral_province ?? 'N/A' }}</div>
                                        <div class="fs-7 text-muted mb-1">District:
                                            {{ $case->referral_district ?? 'N/A' }}</div>
                                        <div class="fs-7 text-muted">Hospital: {{ $case->referral_hospital ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-6 g-xl-9">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Recommended Actions</span>

                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        <span
                                            class="fs-7 text-muted">{{ $case->recommended_action ?? 'No recommended actions recorded.' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - View Case Details-->
@endforeach
