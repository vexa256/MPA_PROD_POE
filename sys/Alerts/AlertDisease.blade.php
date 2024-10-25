<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-md-12">
        <!--begin::Filters-->
        <div class="card mb-5">
            <div class="card-body">
                <form action="{{ route('getPriorityDiseaseAlerts') }}" method="GET" class="mb-8">
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
                            <label class="form-label fs-6 fw-semibold text-gray-700">Province</label>
                            <select name="province" class="form-select form-select-solid" data-control="select2"
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

                        <input type="hidden" name="disease" value="{{ $priorityDisease }}">

                        <div class="col-md-3">
                            <label class="form-label fs-6 fw-semibold text-gray-700">District</label>
                            <select name="district" class="form-select form-select-solid" data-control="select2"
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
                        <div class="col-md-3 mt-5">
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
            </div>
        </div>
        <!--end::Filters-->
        <!--begin::Card-->
        <div class="card card-flush h-xl-100">
            <!--begin::Card header-->
            <div class="card-header pt-7">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Priority Disease Alerts</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Overview of suspected cases for the selected
                        priority disease</span>
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
                <!--begin::Stats summary-->
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="min-w-150px rounded-start">Traveller Name</th>
                                <th class="min-w-100px">Age</th>
                                <th class="min-w-100px">Gender</th>
                                <th class="min-w-150px">Point of Entry</th>
                                <th class="min-w-150px">District</th>
                                <th class="min-w-150px">Province</th>
                                <th class="min-w-150px">Arrival Date</th>
                                <th class="min-w-100px rounded-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suspectedCases as $case)
                                <tr>
                                    <td class="fw-semibold text-gray-800">{{ $case->traveller_name ?? 'N/A' }}</td>
                                    <td>{{ $case->age ?? 'N/A' }}</td>
                                    <td>{{ $case->gender ?? 'N/A' }}</td>
                                    <td>{{ $case->poe_name ?? 'N/A' }}</td>
                                    <td>{{ $case->poe_district ?? 'N/A' }}</td>
                                    <td>{{ $case->poe_province ?? 'N/A' }}</td>
                                    <td>{{ $case->arrival_date ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm shadow-lg show-more-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#caseDetailsModal_{{ $loop->index }}">
                                            More
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal for Showing More Details -->
                                <div class="modal fade" id="caseDetailsModal_{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header pb-0 border-0 justify-content-end">
                                                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"/>
                                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                
                                            <div class="modal-body py-10 px-lg-17">
                                                <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_customer_header" data-kt-scroll-wrappers="#kt_modal_add_customer_scroll" data-kt-scroll-offset="300px">
                                                    <div class="mb-13 text-center">
                                                        <h1 class="mb-3">Case Details for {{ $case->traveller_name }}</h1>
                                                        <div class="text-muted fw-bold fs-5">Review the details of the case below.</div>
                                                    </div>
                                
                                                    <div class="d-flex flex-column flex-xl-row">
                                                        <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
                                                            <div class="card mb-5 mb-xl-8">
                                                                <div class="card-header border-0 pt-5">
                                                                    <h3 class="card-title align-items-start flex-column">
                                                                        <span class="card-label fw-bolder fs-3 mb-1">Personal Information</span>
                                                                    </h3>
                                                                </div>
                                                                <div class="card-body pt-5">
                                                                    <div class="d-flex flex-stack fs-4 py-3">
                                                                        <div class="fw-bolder rotate collapsible" data-bs-toggle="collapse" href="#kt_customer_view_details" role="button" aria-expanded="false" aria-controls="kt_customer_view_details">Details
                                                                            <span class="ms-2 rotate-180">
                                                                                <span class="svg-icon svg-icon-3">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                                                    </svg>
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div id="kt_customer_view_details" class="collapse show">
                                                                        <div class="py-5 fs-6">
                                                                            <div class="fw-bolder mt-5">Traveller Name</div>
                                                                            <div class="text-gray-600">{{ $case->traveller_name ?? 'N/A' }}</div>
                                                                            <div class="fw-bolder mt-5">Age</div>
                                                                            <div class="text-gray-600">{{ $case->age ?? 'N/A' }}</div>
                                                                            <div class="fw-bolder mt-5">Gender</div>
                                                                            <div class="text-gray-600">{{ $case->gender ?? 'N/A' }}</div>
                                                                            <div class="fw-bolder mt-5">Address</div>
                                                                            <div class="text-gray-600">{{ $case->address ?? 'N/A' }}</div>
                                                                            <div class="fw-bolder mt-5">Phone Number</div>
                                                                            <div class="text-gray-600">{{ $case->phone_number ?? 'N/A' }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                
                                                        <div class="flex-lg-row-fluid ms-lg-15">
                                                            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8">
                                                                <li class="nav-item">
                                                                    <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_customer_view_overview_tab">Travel Information</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_customer_view_overview_events_and_logs_tab">Medical Information</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_customer_view_overview_referral_tab">Referral Information</a>
                                                                </li>
                                                            </ul>
                                
                                                            <div class="tab-content" id="myTabContent">
                                                                <div class="tab-pane fade show active" id="kt_customer_view_overview_tab" role="tabpanel">
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Travel Details</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            <div class="table-responsive">
                                                                                <table class="table align-middle table-row-dashed gy-5">
                                                                                    <tbody class="fs-6 fw-bold text-gray-600">
                                                                                        <tr>
                                                                                            <td class="text-muted">Point of Entry</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->poe_name ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">District</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->poe_district ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Province</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->poe_province ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Arrival Date</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->arrival_date ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Departure Country</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->departure_country ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Travel Destination</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->travel_destination ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Transit Countries</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            @if(!empty($case->transit_countries))
                                                                                @php
                                                                                    $transitCountries = json_decode($case->transit_countries, true);
                                                                                @endphp
                                                                                @if ($transitCountries && is_array($transitCountries))
                                                                                    <div class="d-flex flex-column gap-5">
                                                                                        @foreach ($transitCountries as $country)
                                                                                            <div class="d-flex flex-stack">
                                                                                                <div class="symbol symbol-40px me-4">
                                                                                                    <div class="symbol-label fs-2 fw-bold bg-primary text-inverse-primary">{{ substr($country, 0, 1) }}</div>
                                                                                                </div>
                                                                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                                                    <div class="flex-grow-1 me-2">
                                                                                                        <span class="text-gray-800 text-hover-primary fs-6 fw-bolder">{{ $country }}</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @else
                                                                                    <span class="text-muted">N/A</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">N/A</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                
                                                                <div class="tab-pane fade" id="kt_customer_view_overview_events_and_logs_tab" role="tabpanel">
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Suspected Diseases</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            @if(!empty($case->suspected_diseases))
                                                                                @php
                                                                                    $suspectedDiseases = json_decode($case->suspected_diseases, true);
                                                                                @endphp
                                                                                @if ($suspectedDiseases && is_array($suspectedDiseases))
                                                                                    <div class="table-responsive">
                                                                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                                                                            <thead>
                                                                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                                                    <th class="min-w-125px">Disease</th>
                                                                                                    <th class="min-w-125px">Score</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody class="text-gray-600 fw-bold">
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
                                                                                    <span class="text-muted">N/A</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">N/A</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Symptoms</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            @if(!empty($case->symptoms))
                                                                                @php
                                                                                    $symptoms = json_decode($case->symptoms, true);
                                                                                @endphp
                                                                                @if ($symptoms && is_array($symptoms))
                                                                                    <div class="d-flex flex-wrap">
                                                                                        @foreach ($symptoms as $symptom)
                                                                                            <span class="badge badge-light-primary fs-7 m-1">{{ $symptom }}</span>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @else
                                                                                    <span class="text-muted">N/A</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">N/A</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Travel Exposures</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            @if(!empty($case->travel_exposures))
                                                                                @php
                                                                                    $travelExposures = json_decode($case->travel_exposures, true);
                                                                                @endphp
                                                                                @if ($travelExposures && is_array($travelExposures))
                                                                                    <div  class="table-responsive">
                                                                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                                                                            <thead>
                                                                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                                                    <th class="min-w-125px">Exposure</th>
                                                                                                    <th class="min-w-125px">Value</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody class="text-gray-600 fw-bold">
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
                                                                                    <span class="text-muted">N/A</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">N/A</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                
                                                                <div class="tab-pane fade" id="kt_customer_view_overview_referral_tab" role="tabpanel">
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Referral Information</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            <div class="table-responsive">
                                                                                <table class="table align-middle table-row-dashed gy-5">
                                                                                    <tbody class="fs-6 fw-bold text-gray-600">
                                                                                        <tr>
                                                                                            <td class="text-muted">Referral Status</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->referral_status ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Referral Province</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->referral_province ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Referral District</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->referral_district ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Referral Hospital</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->referral_hospital ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Emergency Contact</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            <div class="table-responsive">
                                                                                <table class="table align-middle table-row-dashed gy-5">
                                                                                    <tbody class="fs-6 fw-bold text-gray-600">
                                                                                        <tr>
                                                                                            <td class="text-muted">Name</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->emergency_contact_name ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class="text-muted">Phone</td>
                                                                                            <td class="fw-bolder text-end">{{ $case->emergency_contact_phone ?? 'N/A' }}</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card pt-4 mb-6 mb-xl-9">
                                                                        <div class="card-header border-0">
                                                                            <div class="card-title">
                                                                                <h2>Recommended Actions</h2>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body pt-0 pb-5">
                                                                            <div class="text-gray-600 fw-bold">
                                                                                {{ $case->recommended_action ?? 'N/A' }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
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
