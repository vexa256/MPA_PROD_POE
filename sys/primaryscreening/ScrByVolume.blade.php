<div class="">
    <!-- Filters Section -->
    <form action="{{ route('ScreeningVolumebyPOE') }}" method="GET">
        @csrf
        <div class="row mb-5">
            <div class="col-md-6">
                <!-- POE Filter -->
                <label class="form-label">Select Point of Entry (POE)</label>
                <select name="poe_id" class="form-select form-select-solid" data-control="select2"
                    data-placeholder="Select a POE">
                    <option value="">All POEs</option>
                    @foreach ($pointsOfEntry as $poe)
                        <option value="{{ $poe->id }}" {{ $filters['poe_id'] == $poe->id ? 'selected' : '' }}>
                            {{ $poe->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <!-- Start Date Filter -->
                <label class="form-label">Start Date</label>
                <input type="text" name="start_date" class="form-control DateArea" placeholder="Select start date"
                    value="{{ $filters['start_date'] ?? '' }}" />
            </div>

            <div class="col-md-3 ">
                <!-- End Date Filter -->
                <label class="form-label">End Date</label>
                <input type="text" name="end_date" class="form-control DateArea" placeholder="Select end date"
                    value="{{ $filters['end_date'] ?? '' }}" />
            </div>

            <div class="row mt-4 mb-5">
                <div class="col-md-6 d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter <!-- Icon for filtering, using Font Awesome -->
                    </button>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <a href="{{ url('/ScreeningVolumebyPOE') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-sync-alt"></i> Reset Filters <!-- Icon for resetting, using Font Awesome -->
                    </a>
                </div>
            </div>


        </div>
    </form>

    <!-- Bar Chart Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Screening Volume by Province (Bar)</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Overview of screenings per province</span>
                    </h3>
                </div>
                <div class="card-body pt-7">
                    <canvas id="provinceChart" width="500" height="400"></canvas>
                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Screening Volume by Province (Line)</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Overview of screenings per province</span>
                    </h3>
                </div>
                <div class="card-body pt-7">
                    <canvas id="provinceLineChart" width="500" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>


    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}

    <div class="container-fluid py-5">
        <!-- Key Metrics (KPIs) Section -->
        <div class="row mb-5">
            <!-- Total Screenings -->
            <div class="col-md-4">
                <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                    <div class="card-body text-center">
                        <span class="fs-3 fw-bold text-gray-800">Total Screenings</span>
                        <span class="fs-6 fw-semibold text-gray-400 d-block mb-3">Based on selected filters</span>
                        <span class="fs-2x fw-bold text-dark">{{ $scr_vol->sum('total_screenings') }}</span>
                    </div>
                </div>
            </div>

            <!-- Busiest POE -->
            <div class="col-md-4">
                <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                    <div class="card-body text-center">
                        <span class="fs-3 fw-bold text-gray-800">Busiest POE</span>
                        <span class="fs-6 fw-semibold text-gray-400 d-block mb-3">Highest screening volume</span>
                        <span class="fs-2x fw-bold text-dark">
                            {{ $scr_vol->sortByDesc('total_screenings')->first()->poe_name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Screening Volume by Province -->
            <div class="col-md-4">
                <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                    <div class="card-body text-center">
                        <span class="fs-3 fw-bold text-gray-800">Screening Volume by Province</span>
                        <span class="fs-6 fw-semibold text-gray-400 d-block mb-3">Summary</span>
                        <span class="fs-2x fw-bold text-dark">
                            {{ $provinceDistribution->sum('total_screenings') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Section: Screening Volume by POE -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Screening Volume by POE</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Detailed screening statistics for each
                                POE</span>
                        </h3>
                    </div>

                    <div class="card-body pt-7">
                        <div class="table-responsive">
                            <table class="table mytable table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-150px">POE Name</th>
                                        <th class="min-w-100px">District</th>
                                        <th class="min-w-100px">Province</th>
                                        <th class="min-w-100px">Total Screenings</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($scr_vol as $data)
                                        <tr>
                                            <td>
                                                <a href="#" class="text-dark fw-bold text-hover-primary fs-6">
                                                    {{ $data->poe_name }}
                                                </a>
                                            </td>
                                            <td>{{ $data->district ?? 'N/A' }}</td>
                                            <td>{{ $data->province ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="text-dark fw-bold d-block fs-6">{{ $data->total_screenings }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Province-wise Screening Breakdown -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card card-flush h-xl-100 shadow-sm hover-elevate-up">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark">Province-wise Screening Breakdown</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Breakdown by province, district, and
                                POE</span>
                        </h3>
                    </div>

                    <div class="card-body pt-7">
                        <div class="table-responsive">
                            <table class="table mytable table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="min-w-150px">Province</th>
                                        {{-- <th class="min-w-150px">District</th>
                                        <th class="min-w-150px">POE Name</th> --}}
                                        <th class="min-w-100px">Total Screenings</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($provinceDistribution as $data)
                                        <tr>
                                            <td>{{ $data->province }}</td>
                                            {{-- <td>{{ $data->district ?? 'N/A' }}</td>
                                            <td>{{ $data->poe_name ?? 'N/A' }}</td> --}}
                                            <td>
                                                <span
                                                    class="text-dark fw-bold d-block fs-6">{{ $data->total_screenings }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
    {{-- DATA TABLES --}}
</div>
