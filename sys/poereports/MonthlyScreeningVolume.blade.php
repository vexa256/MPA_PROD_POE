<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">{{ $Desc }}</h3>
    </div>
    <div class="card-body">
        <div class="mb-5">
            <!-- Form for Filtering -->
            <form action="{{ route('monthlyScreeningVolumeByPOE') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <select name="year" class="form-select form-select-solid" data-control="select2"
                        data-placeholder="Select Year">
                        @for ($i = date('Y'); $i >= 2000; $i--)
                            <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <select name="poeid" class="form-select form-select-solid" data-control="select2"
                        data-placeholder="Select POE">
                        <option value="">All POEs</option>
                        @foreach ($pointsOfEntry as $poe)
                            <option value="{{ $poe->id }}" {{ $selectedPOEId == $poe->id ? 'selected' : '' }}>
                                {{ $poe->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
                <div class="col-auto">
                    <a href="{{ url('/monthlyScreeningVolumeByPOE') }}" class="btn btn-danger filters">Reset</a>
                </div>
            </form>
        </div>

        <!-- Two Charts Side-by-Side -->
        <div class="row">
            <div class="col-6">
                <div id="monthly-screening-chart" style="height: 400px;"></div>
            </div>
            <div class="col-6">
                <div id="monthly-screening-line-chart" style="height: 400px;"></div>
            </div>
        </div>

        <div class="card-body">
            <!-- Responsive Table Section -->
            <div id="table-section" class="">
                <h5 class="mb-5 pb-5">Monthly Screening Data by POE</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                {{-- <th>POE Name</th> --}}
                                <th>Total Screenings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($screeningData as $data)
                                <tr>
                                    <td>{{ date('F', mktime(0, 0, 0, $data->month, 10)) }}</td>
                                    {{-- <td>{{ $pointsOfEntry->firstWhere('id', $data->poe_id)->name ?? 'Unknown' }}</td> --}}
                                    <td>{{ number_format($data->total_screenings) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var screeningData = @json($screeningData);
        var monthlyData = [];
        var categories = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Prepare data for the charts
        categories.forEach(function(month, index) {
            var monthData = screeningData.find(data => data.month == (index + 1));
            monthlyData.push(monthData ? monthData.total_screenings : 0);
        });

        // Column Chart Options
        var columnOptions = {
            series: [{
                name: 'Monthly Screenings',
                type: 'column',
                data: monthlyData
            }],
            chart: {
                height: 400,
                type: 'bar',
                fontFamily: 'Segoe UI, sans-serif'
            },
            title: {
                text: 'Monthly POE Screening Volume (Column)',
                align: 'left'
            },
            xaxis: {
                categories: categories
            },
            yaxis: {
                labels: {
                    formatter: val => Math.round(val)
                }
            },
            colors: ['#0078D4']
        };
        var columnChart = new ApexCharts(document.querySelector("#monthly-screening-chart"), columnOptions);
        columnChart.render();

        // Line Chart Options
        var lineOptions = {
            series: [{
                name: 'Monthly Screenings',
                type: 'line',
                data: monthlyData
            }],
            chart: {
                height: 400,
                type: 'line',
                fontFamily: 'Segoe UI, sans-serif'
            },
            title: {
                text: 'Monthly POE Screening Volume (Line)',
                align: 'left'
            },
            xaxis: {
                categories: categories
            },
            yaxis: {
                labels: {
                    formatter: val => Math.round(val)
                }
            },
            colors: ['#50E6FF']
        };
        var lineChart = new ApexCharts(document.querySelector("#monthly-screening-line-chart"), lineOptions);
        lineChart.render();
    });
</script>

<!-- Styling -->
<style>
    .card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }

    .card-header {
        background-color: #f3f2f1;
        border-bottom: 1px solid #edebe9;
        padding: 16px 20px;
    }

    .card-title {
        color: #323130;
        font-size: 18px;
        font-weight: 600;
    }

    .btn-light {
        background-color: #ffffff;
        border-color: #8a8886;
        color: #323130;
    }

    .btn-primary {
        background-color: #0078d4;
        border-color: #0078d4;
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #106ebe;
        border-color: #106ebe;
    }

    .form-select-solid {
        background-color: #f3f2f1;
        border-color: #8a8886;
        color: #323130;
    }

    .table-responsive {
        max-width: 100%;
        overflow-x: auto;
    }

    .table {
        margin-bottom: 0;
    }

    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
    }
</style>



<!-- Styling -->
<style>
    .card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }

    .card-header {
        background-color: #f3f2f1;
        border-bottom: 1px solid #edebe9;
        padding: 16px 20px;
    }

    .card-title {
        color: #323130;
        font-size: 18px;
        font-weight: 600;
    }

    .btn-light {
        background-color: #ffffff;
        border-color: #8a8886;
        color: #323130;
    }

    .btn-primary {
        background-color: #0078d4;
        border-color: #0078d4;
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #106ebe;
        border-color: #106ebe;
    }

    .form-select-solid {
        background-color: #f3f2f1;
        border-color: #8a8886;
        color: #323130;
    }

    #daily-screening-chart {
        background-color: #ffffff;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
    }
</style>
