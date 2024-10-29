<div class="row g-5 g-xl-8">
    <!-- Filter Section -->
    <div class="col-xl-12 mb-5">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('TravelRouteAnalysis') }}" class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-solid"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control form-control-solid"
                            value="{{ request('end_date') }}">
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
                        <label class="form-label">Disease</label>
                        <select name="disease" class="form-select form-select-solid">
                            <option value="">All Diseases</option>
                            @foreach ($diseases as $disease)
                                <option value="{{ $disease }}"
                                    {{ request('disease') == $disease ? 'selected' : '' }}>
                                    {{ $disease }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transit Country Analysis Chart -->
    <div class="col-12 mb-5">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Most Common Transit Countries</h3>
            </div>
            <div class="card-body">
                <div id="transitCountryChart"></div>
            </div>
        </div>
    </div>

    <!-- Destination Country Analysis Chart -->
    <div class="col-12 mb-5">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Most Common Destinations for Suspected Cases</h3>
            </div>
            <div class="card-body">
                <div id="destinationCountryChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Data for Transit Countries Chart
    const transitCountryData = @json($transitCountryCounts);
    const transitCountryLabels = Object.keys(transitCountryData);
    const transitCountryValues = Object.values(transitCountryData);

    // Data for Destination Countries Chart
    const destinationCountryData = @json($destinationCounts);
    const destinationCountryLabels = Object.keys(destinationCountryData);
    const destinationCountryValues = Object.values(destinationCountryData);

    // Colors for bars (alternating colors for better visibility)
    const colorPalette = ['#6993FF', '#FFA800', '#2BBF94', '#FF5733', '#9B59B6', '#FFC107', '#27AE60', '#2980B9'];

    // Transit Country Chart Options
    var transitCountryOptions = {
        series: [{
            name: 'Transit Frequency',
            data: transitCountryValues
        }],
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: true
            }
        },
        colors: colorPalette,
        title: {
            text: 'Transit Countries for Suspected Cases',
            align: 'center',
            style: {
                fontSize: '18px',
                fontWeight: 'bold',
                color: '#333'
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '60%',
                distributed: true
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '12px',
                colors: ['#333']
            }
        },
        xaxis: {
            categories: transitCountryLabels,
            title: {
                text: 'Number of Suspected Cases',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    color: '#333'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '12px',
                    color: '#333'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " cases";
                }
            }
        }
    };

    // Destination Country Chart Options
    var destinationCountryOptions = {
        series: [{
            name: 'Destination Frequency',
            data: destinationCountryValues
        }],
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: true
            }
        },
        colors: colorPalette,
        title: {
            text: 'Most Common Destinations for Suspected Cases',
            align: 'center',
            style: {
                fontSize: '18px',
                fontWeight: 'bold',
                color: '#333'
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '60%',
                distributed: true
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '12px',
                colors: ['#333']
            }
        },
        xaxis: {
            categories: destinationCountryLabels,
            title: {
                text: 'Number of Suspected Cases',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            labels: {
                style: {
                    fontSize: '12px',
                    color: '#333'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '12px',
                    color: '#333'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " cases";
                }
            }
        }
    };

    // Render Charts
    new ApexCharts(document.querySelector("#transitCountryChart"), transitCountryOptions).render();
    new ApexCharts(document.querySelector("#destinationCountryChart"), destinationCountryOptions).render();
</script>
