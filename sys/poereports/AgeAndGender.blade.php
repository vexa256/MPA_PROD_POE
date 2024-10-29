<div class="row g-5 g-xl-8">
    <!-- Filter Section -->
    <div class="col-xl-12 mb-5">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('AgeGenderAnalysis') }}" class="row align-items-end">
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

    <!-- Gender Distribution Chart -->
    <div class="col-xl-12 mb-5">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Gender Distribution by Suspected Disease</h3>
            </div>
            <div class="card-body">
                <div id="genderDistributionChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Filter out diseases with empty names or zero counts
    const filteredGenderData = Object.entries(@json($genderData))
        .filter(([disease, counts]) =>
            disease.trim() !== '' && (counts.male > 0 || counts.female > 0 || counts.other > 0)
        );

    const categories = filteredGenderData.map(([disease]) => disease);
    const maleData = filteredGenderData.map(([_, counts]) => counts.male);
    const femaleData = filteredGenderData.map(([_, counts]) => counts.female);
    const otherData = filteredGenderData.map(([_, counts]) => counts.other);

    // Gender Distribution Chart
    var genderOptions = {
        series: [{
                name: 'Male',
                data: maleData
            },
            {
                name: 'Female',
                data: femaleData
            },
            {
                name: 'Other',
                data: otherData
            }
        ],
        chart: {
            type: 'bar',
            height: 400
        },
        title: {
            text: 'Gender Distribution by Suspected Disease'
        },
        xaxis: {
            categories: categories
        }
    };

    // Render the chart
    new ApexCharts(document.querySelector("#genderDistributionChart"), genderOptions).render();
</script>
