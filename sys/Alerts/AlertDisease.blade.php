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
                                <th class="min-w-150px rounded-start">Suspected Disease</th>
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
                                    <td class="fw-semibold text-gray-800">{{ $priorityDisease }}</td>
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
                                <!-- Modal for Showing More Details -->
                                <div class="modal fade" id="caseDetailsModal_{{ $loop->index }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Case Details for {{ $case->traveller_name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Traveller Name:</strong>
                                                        {{ $case->traveller_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Age:</strong> {{ $case->age ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Gender:</strong> {{ $case->gender ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Address:</strong> {{ $case->address ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Phone Number:</strong>
                                                        {{ $case->phone_number ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Point of Entry:</strong> {{ $case->poe_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>District:</strong> {{ $case->poe_district ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Province:</strong> {{ $case->poe_province ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Arrival Date:</strong>
                                                        {{ $case->arrival_date ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Departure Country:</strong>
                                                        {{ $case->departure_country ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Travel Destination:</strong>
                                                        {{ $case->travel_destination ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <strong>Transit Countries:</strong>
                                                        @if (!empty($case->transit_countries))
                                                            @php
                                                                $transitCountries = json_decode(
                                                                    $case->transit_countries,
                                                                    true,
                                                                );
                                                            @endphp
                                                            @if ($transitCountries && is_array($transitCountries))
                                                                <ul>
                                                                    @foreach ($transitCountries as $country)
                                                                        <li>{{ $country }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p>N/A</p>
                                                            @endif
                                                        @else
                                                            <p>N/A</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <strong>Suspected Diseases:</strong>
                                                        @if (!empty($case->suspected_diseases))
                                                            @php
                                                                $suspectedDiseases = json_decode(
                                                                    $case->suspected_diseases,
                                                                    true,
                                                                );
                                                            @endphp
                                                            @if ($suspectedDiseases && is_array($suspectedDiseases))
                                                                <ul>
                                                                    @foreach ($suspectedDiseases as $disease)
                                                                        <li>{{ $disease['disease'] ?? 'N/A' }} (Score:
                                                                            {{ $disease['score'] ?? 'N/A' }})</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p>N/A</p>
                                                            @endif
                                                        @else
                                                            <p>N/A</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <strong>Symptoms:</strong>
                                                        @if (!empty($case->symptoms))
                                                            @php
                                                                $symptoms = json_decode($case->symptoms, true);
                                                            @endphp
                                                            @if ($symptoms && is_array($symptoms))
                                                                <ul>
                                                                    @foreach ($symptoms as $symptom)
                                                                        <li>{{ $symptom }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p>N/A</p>
                                                            @endif
                                                        @else
                                                            <p>N/A</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <strong>Travel Exposures:</strong>
                                                        @if (!empty($case->travel_exposures))
                                                            @php
                                                                $travelExposures = json_decode(
                                                                    $case->travel_exposures,
                                                                    true,
                                                                );
                                                            @endphp
                                                            @if ($travelExposures && is_array($travelExposures))
                                                                <ul>
                                                                    @foreach ($travelExposures as $exposure => $value)
                                                                        <li>{{ ucfirst($exposure) }}:
                                                                            {{ $value }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p>N/A</p>
                                                            @endif
                                                        @else
                                                            <p>N/A</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Emergency Contact Name:</strong>
                                                        {{ $case->emergency_contact_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Emergency Contact Phone:</strong>
                                                        {{ $case->emergency_contact_phone ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <strong>Recommended Actions:</strong>
                                                        {{ $case->recommended_action ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Referral Status:</strong>
                                                        {{ $case->referral_status ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Referral Province:</strong>
                                                        {{ $case->referral_province ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Referral District:</strong>
                                                        {{ $case->referral_district ?? 'N/A' }}
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Referral Hospital:</strong>
                                                        {{ $case->referral_hospital ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                    data-bs-dismiss="modal">Close</button>
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
