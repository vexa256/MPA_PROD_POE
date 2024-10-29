<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-md-12">
        <!--begin::Filters-->
        <div class="card mb-5">
            <div class="card-body">
                <form action="{{ route('GetClassificationData') }}" method="GET" class="mb-8">
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
                        <input type="hidden" name="classification" value="{{ $classification }}">
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
                    <span class="card-label fw-bold fs-3 mb-1">Priority Classification Alerts</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Overview of cases for classification:
                        {{ $classification }}</span>
                </h3>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_classifications">
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
                                        data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_case_details_{{ $loop->index }}">
                                        View Details
                                    </button>
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

<!-- Case Details Modals -->
@foreach ($suspectedCases as $case)
    <div class="modal fade" id="kt_modal_case_details_{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_case_details_header">
                    <h2 class="fw-bold">Case Details for {{ $case->traveller_name }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close"
                        data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_case_details_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_case_details_header"
                        data-kt-scroll-wrappers="#kt_modal_case_details_scroll" data-kt-scroll-offset="300px">
                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <!-- Traveller Information -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-50px me-5">
                                                <span class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-profile-circle fs-1 text-primary"><span
                                                            class="path1"></span><span class="path2"></span><span
                                                            class="path3"></span></i>
                                                </span>
                                            </div>
                                            <div>
                                                <a href="#"
                                                    class="fs-4 text-gray-800 text-hover-primary fw-bold">Traveller
                                                    Information</a>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Name:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->traveller_name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Age:</span>
                                                <span class="text-gray-800 fw-bold">{{ $case->age ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Gender:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->gender ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Address:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->address ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Phone:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->phone_number ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="flex-grow-1 text-muted fw-semibold">ID Number:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->id_number ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- POE Information -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-50px me-5">
                                                <span class="symbol-label bg-light-warning">
                                                    <i class="ki-duotone ki-geolocation fs-1 text-warning"><span
                                                            class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div>
                                                <a href="#"
                                                    class="fs-4 text-gray-800 text-hover-primary fw-bold">Point of
                                                    Entry</a>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Name:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->poe_name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Type:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->poe_type ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">District:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->poe_district ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Province:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->poe_province ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="flex-grow-1 text-muted fw-semibold">POE ID:</span>
                                                <span class="text-gray-800 fw-bold">{{ $case->poeid ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Screening Information -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-50px me-5">
                                                <span class="symbol-label bg-light-info">
                                                    <i class="ki-duotone ki-document fs-1 text-info"><span
                                                            class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div>
                                                <a href="#"
                                                    class="fs-4 text-gray-800 text-hover-primary fw-bold">Screening
                                                    Info</a>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Screener ID:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->screener_id ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Screener
                                                    Username:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->screener_username ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Classification:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->classification ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Confidence
                                                    Level:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->confidence_level ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="flex-grow-1 text-muted fw-semibold">Recommended
                                                    Action:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->recommended_action ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- High Risk Alert -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-flex flex-column p-8">
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-50px me-5">
                                                <span class="symbol-label bg-light-danger">
                                                    <i class="ki-duotone ki-shield-tick fs-1 text-danger"><span
                                                            class="path1"></span><span class="path2"></span></i>
                                                </span>
                                            </div>
                                            <div>
                                                <a href="#"
                                                    class="fs-4 text-gray-800 text-hover-primary fw-bold">Risk
                                                    Assessment</a>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">High Risk
                                                    Alert:</span>
                                                <span
                                                    class="badge badge-light-{{ $case->high_risk_alert ? 'danger' : 'success' }} fw-bold">{{ $case->high_risk_alert ? 'Yes' : 'No' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="flex-grow-1 text-muted fw-semibold">Endemic
                                                    Warning:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->endemic_warning ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <!-- Suspected Diseases -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Suspected Diseases</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @php
                                            $suspectedDiseases = json_decode($case->suspected_diseases, true);
                                        @endphp
                                        @if ($suspectedDiseases && is_array($suspectedDiseases))
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
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
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="progress h-6px w-100 me-2">
                                                                            <div class="progress-bar bg-primary"
                                                                                role="progressbar"
                                                                                style="width: {{ ($disease['score'] ?? 0) * 100 }}%"
                                                                                aria-valuenow="{{ ($disease['score'] ?? 0) * 100 }}"
                                                                                aria-valuemin="0" aria-valuemax="100">
                                                                            </div>
                                                                        </div>
                                                                        <span
                                                                            class="text-muted fs-7 fw-semibold">{{ ($disease['score'] ?? 0) * 100 }}%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <span class="fs-7 text-muted">No suspected diseases recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Symptoms -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold  text-gray-800">Symptoms</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @php
                                            $symptoms = json_decode($case->symptoms, true);
                                        @endphp
                                        @if ($symptoms && is_array($symptoms))
                                            <div class="d-flex flex-wrap">
                                                @foreach ($symptoms as $symptom)
                                                    <span
                                                        class="badge badge-light-primary fs-7 m-1">{{ $symptom }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="fs-7 text-muted">No symptoms recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                            <!-- Travel Exposures -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Travel Exposures</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @php
                                            $travelExposures = json_decode($case->travel_exposures, true);
                                        @endphp
                                        @if ($travelExposures && is_array($travelExposures))
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
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
                                                                <td>
                                                                    <span
                                                                        class="badge badge-light-{{ $value ? 'success' : 'danger' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <span class="fs-7 text-muted">No travel exposures recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Transit Countries -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Transit Countries</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        @php
                                            $transitCountries = json_decode($case->transit_countries, true);
                                        @endphp
                                        @if ($transitCountries && is_array($transitCountries))
                                            <div class="d-flex flex-wrap">
                                                @foreach ($transitCountries as $country)
                                                    <span
                                                        class="badge badge-light-info fs-7 m-1">{{ $country }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="fs-7 text-muted">No transit countries recorded.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-6 g-xl-9">
                            <!-- Referral Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Referral Information</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Status:</span>
                                                <span
                                                    class="badge badge-light-primary">{{ $case->referral_status ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">Province:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->referral_province ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="flex-grow-1 text-muted fw-semibold">District:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->referral_district ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="flex-grow-1 text-muted fw-semibold">Hospital:</span>
                                                <span
                                                    class="text-gray-800 fw-bold">{{ $case->referral_hospital ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Additional Notes</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3">
                                        <span
                                            class="fs-7 text-gray-800">{{ $case->additional_notes ?? 'No additional notes recorded.' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Close Button -->
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
<!-- End of Case Details Modals -->
