<div class="row g-5 g-xl-8">
    <!-- Filter Section -->
    <div class="col-xl-12 mb-5">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('ageDistribution') }}" class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select form-select-solid">
                            <option value="">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select form-select-solid">
                            <option value="">All Years</option>
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Point of Entry (POE)</label>
                        <select name="poe_id" class="form-select form-select-solid">
                            <option value="">All POEs</option>
                            @foreach ($pointsOfEntry as $poe)
                                <option value="{{ $poe->id }}"
                                    {{ request('poe_id') == $poe->id ? 'selected' : '' }}>
                                    {{ $poe->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mt-7">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Age Distribution Chart -->
    <div class="col-xl-12 mb-5">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Age Distribution by Suspected Disease</h3>
            </div>
            <div class="card-body">
                <div id="ageDistributionChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Prepare data for the Age Distribution Chart
    const ageData = @json($ageData);

    // Transform data into a format suitable for ApexCharts box plot
    const series = Object.entries(ageData).map(([disease, ages]) => {
        ages.sort((a, b) => a - b); // Sort ages for box plot calculation
        const q1 = ages[Math.floor(ages.length * 0.25)];
        const q3 = ages[Math.floor(ages.length * 0.75)];
        const min = ages[0];
        const max = ages[ages.length - 1];
        const median = ages[Math.floor(ages.length * 0.5)];

        return {
            x: disease,
            y: [min, q1, median, q3, max]
        };
    });

    var ageOptions = {
        series: [{
            name: 'Age Distribution',
            data: series
        }],
        chart: {
            type: 'boxPlot',
            height: 400
        },
        title: {
            text: 'Age Distribution by Suspected Disease'
        },
        xaxis: {
            type: 'category',
            title: {
                text: 'Suspected Diseases'
            }
        },
        yaxis: {
            title: {
                text: 'Age'
            }
        }
    };

    // Render the chart
    new ApexCharts(document.querySelector("#ageDistributionChart"), ageOptions).render();
</script>
