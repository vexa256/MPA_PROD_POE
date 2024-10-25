<div class="container-fluid py-5">
    <!-- Filters Section -->
    <form action="{{ route('GenderAndRouteAnalysisDashboard') }}" method="GET">
        @csrf
        <div class="row mb-5">
            <!-- POE Filter -->
            <div class="col-md-6">
                <label class="form-label">Select Point of Entry (POE)</label>
                <select name="poe_id" class="form-select form-select-solid" data-control="select2"
                    data-placeholder="Select a POE">
                    <option value="">All POEs</option>
                    @foreach ($pointsOfEntry as $poe)
                        <option value="{{ $poe->id }}"
                            {{ isset($filters['poe_id']) && $filters['poe_id'] == $poe->id ? 'selected' : '' }}>
                            {{ $poe->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date Filter -->
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" placeholder="Select start date"
                    value="{{ $filters['start_date'] ?? '' }}" />
            </div>

            <!-- End Date Filter -->
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" placeholder="Select end date"
                    value="{{ $filters['end_date'] ?? '' }}" />
            </div>
        </div>

        <div class="row mt-4 mb-5">
            <div class="col-md-6 d-flex justify-content-start">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="{{ route('GenderAndRouteAnalysisDashboard') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-sync-alt"></i> Reset Filters
                </a>
            </div>
        </div>
    </form>

    <!-- Key Metrics Section -->
    <div class="row">
        <!-- Total Screened -->
        <div class="col-md-6">
            <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up text-center">
                <div class="card-body">
                    <span class="fs-3 fw-bold text-gray-800">Total Screened</span>
                    <span class="fs-6 fw-semibold text-gray-400 d-block mb-3">Combined primary and secondary
                        screenings</span>
                    <span class="fs-2x fw-bold text-dark">
                        {{ number_format($totalScreened) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Most Screened Gender -->
        <div class="col-md-6">
            <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up text-center">
                <div class="card-body">
                    <span class="fs-3 fw-bold text-gray-800">Most Screened Gender</span>
                    <span class="fs-6 fw-semibold text-gray-400 d-block mb-3">Across all screenings</span>
                    <span class="fs-2x fw-bold text-dark">
                        {{ $mostScreenedGender ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <!-- Chart 1: Gender Distribution -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                <div class="card-header d-flex justify-content-between align-items-center pt-5">
                    <div>
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Gender Distribution</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Total screenings by gender</span>
                        </h3>
                    </div>
                    <button class="btn btn-sm btn-light-primary" data-bs-toggle="modal"
                        data-bs-target="#genderDistributionModal">
                        View Data
                    </button>
                </div>
                <div class="card-body pt-7">
                    <canvas id="genderDistributionChart" width="500" height="300"></canvas>
                </div>
            </div>

            <!-- Modal for Gender Distribution -->
            <div class="modal fade" id="genderDistributionModal" tabindex="-1"
                aria-labelledby="genderDistributionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="genderDistributionModalLabel">Gender Distribution Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <!-- Modal Body with table -->
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="min-w-150px">Gender</th>
                                            <th class="min-w-100px">Total Screened</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($genderCounts as $gender => $count)
                                            <tr>
                                                <td>{{ $gender }}</td>
                                                <td>{{ number_format($count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Genders Screened Per Province -->
        <div class="col-md-6">
            <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                <div class="card-header d-flex justify-content-between align-items-center pt-5">
                    <div>
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Genders Screened Per Province</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Screenings by gender and province</span>
                        </h3>
                    </div>
                    <button class="btn btn-sm btn-light-primary" data-bs-toggle="modal"
                        data-bs-target="#gendersPerProvinceModal">
                        View Data
                    </button>
                </div>
                <div class="card-body pt-7">
                    <canvas id="gendersPerProvinceChart" width="500" height="300"></canvas>
                </div>
            </div>

            <!-- Modal for Genders Screened Per Province -->
            <div class="modal fade" id="gendersPerProvinceModal" tabindex="-1"
                aria-labelledby="gendersPerProvinceModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="gendersPerProvinceModalLabel">Genders Screened Per Province
                                Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <!-- Modal Body with table -->
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="min-w-150px">Province</th>
                                            <th class="min-w-100px">Gender</th>
                                            <th class="min-w-100px">Total Screened</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($gendersScreenedPerProvince as $province => $genderCounts)
                                            @foreach ($genderCounts as $gender => $count)
                                                <tr>
                                                    <td>{{ $province }}</td>
                                                    <td>{{ $gender }}</td>
                                                    <td>{{ number_format($count) }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Genders Screened Per Point of Entry -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card card-flush shadow-sm hover-elevate-up">
                <div class="card-header pt-5">
                    <h3 class="card-title">
                        <span class="card-label fw-bold text-dark">Genders Screened Per Point of Entry</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Screenings by gender and POE</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="min-w-150px">POE Name</th>
                                    <th class="min-w-100px">Gender</th>
                                    <th class="min-w-100px">Total Screened</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gendersPerPOE as $poeData)
                                    @foreach ($poeData['gender_counts'] as $gender => $count)
                                        <tr>
                                            <td>{{ $poeData['poe_name'] }}</td>
                                            <td>{{ $gender }}</td>
                                            <td>{{ number_format($count) }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
