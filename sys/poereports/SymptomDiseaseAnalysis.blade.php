<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $Title }}</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-primary">
            {{ $Desc }}
        </div>

        <!-- Filters -->
        <div class="row mb-5">
            <div class="col-md-4">
                <label class="form-label">Year</label>
                <select class="form-select" id="year-select" name="year">
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Month</label>
                <select class="form-select" id="month-select" name="month">
                    <option value="">All Months</option>
                    @foreach ($months as $month)
                        <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Point of Entry</label>
                <select class="form-select" id="poe-select" name="poeid">
                    <option value="">All POEs</option>
                    @foreach ($pointsOfEntry as $poe)
                        <option value="{{ $poe->id }}" {{ $selectedPOEId == $poe->id ? 'selected' : '' }}>
                            {{ $poe->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Chart -->
        <div class="row g-5 g-xl-8">
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Comprehensive Symptom-Disease Analysis</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Distribution of symptoms and suspected
                                diseases across points of entry</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="comprehensive_chart" style="height: 600px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const heatmapData = @json($HeatmapData);
        const stackedBarData = @json($StackedBarData);

        console.log('Heatmap Data:', heatmapData);
        console.log('Stacked Bar Data:', stackedBarData);

        // Function to parse JSON strings
        function parseJsonString(str) {
            try {
                return JSON.parse(str);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                return [];
            }
        }

        // Extract unique POEs, diseases, and symptoms
        const poes = [...new Set(stackedBarData.map(item => item.poe_name))];
        const diseases = [...new Set(stackedBarData.map(item => {
            const parsedDisease = item.disease ? parseJsonString(item.disease)[0] : 'Unknown';
            return parsedDisease || 'Unknown';
        }))];
        const symptoms = [...new Set(heatmapData.flatMap(item => {
            const parsedSymptoms = parseJsonString(item.symptom);
            return Array.isArray(parsedSymptoms) ? parsedSymptoms : [];
        }))];

        // Prepare data for the chart
        const series = diseases.map(disease => ({
            name: disease,
            data: poes.map(poe => {
                const entries = stackedBarData.filter(item =>
                    item.poe_name === poe &&
                    (item.disease ? parseJsonString(item.disease)[0] : 'Unknown') ===
                    disease
                );
                const totalCount = entries.reduce((sum, entry) => sum + entry.symptom_count,
                    0);
                return {
                    x: poe,
                    y: totalCount,
                    symptoms: symptoms.map(symptom => {
                        const matchingHeatmapEntries = heatmapData.filter(item =>
                            item.poe_id === entries[0]?.poe_id &&
                            parseJsonString(item.disease)[0] === disease &&
                            parseJsonString(item.symptom).includes(symptom)
                        );
                        return matchingHeatmapEntries.reduce((sum, entry) => sum +
                            entry.frequency, 0);
                    })
                };
            })
        }));

        const options = {
            series: series,
            chart: {
                type: 'bar',
                height: 600,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const dataPoint = config.w.config.series[config.seriesIndex].data[config
                            .dataPointIndex];
                        showSymptomBreakdown(dataPoint, config.w.config.series[config.seriesIndex]
                        .name);
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 5,
                    dataLabels: {
                        total: {
                            enabled: true,
                            style: {
                                fontSize: '13px',
                                fontWeight: 900
                            }
                        }
                    }
                },
            },
            xaxis: {
                categories: poes,
                labels: {
                    rotate: -45,
                    trim: true,
                    maxHeight: 120
                }
            },
            yaxis: {
                title: {
                    text: 'Number of Cases'
                },
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            colors: ['#FF4560', '#008FFB', '#00E396', '#FEB019', '#775DD0', '#546E7A', '#26a69a',
                '#D10CE8'],
            title: {
                text: 'Comprehensive Symptom-Disease Analysis by Point of Entry',
                align: 'center',
                style: {
                    fontSize: '18px'
                }
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " cases"
                    }
                }
            }
        };

        try {
            const chart = new ApexCharts(document.querySelector("#comprehensive_chart"), options);
            chart.render();
        } catch (error) {
            console.error('Error rendering chart:', error);
            document.querySelector("#comprehensive_chart").innerHTML =
                '<p class="text-danger">Error: Unable to render chart. Please check the console for more details.</p>';
        }

        function showSymptomBreakdown(dataPoint, disease) {
            const symptomBreakdown = dataPoint.symptoms.map((count, index) => ({
                x: symptoms[index],
                y: count
            })).filter(item => item.y > 0).sort((a, b) => b.y - a.y);

            const breakdownOptions = {
                series: [{
                    name: 'Symptom Frequency',
                    data: symptomBreakdown.map(item => item.y)
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: symptomBreakdown.map(item => item.x),
                },
                title: {
                    text: `Symptom Breakdown for ${disease} at ${dataPoint.x}`,
                    align: 'center'
                }
            };

            // Create a modal to display the symptom breakdown
            const modal = document.createElement('div');
            modal.style.position = 'fixed';
            modal.style.left = '0';
            modal.style.top = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex';
            modal.style.justifyContent = 'center';
            modal.style.alignItems = 'center';

            const modalContent = document.createElement('div');
            modalContent.style.backgroundColor = 'white';
            modalContent.style.padding = '20px';
            modalContent.style.borderRadius = '10px';
            modalContent.style.width = '80%';
            modalContent.style.maxWidth = '800px';

            const closeButton = document.createElement('button');
            closeButton.textContent = 'Close';
            closeButton.className = 'btn btn-secondary mt-3';
            closeButton.onclick = function() {
                document.body.removeChild(modal);
            };

            const chartDiv = document.createElement('div');
            modalContent.appendChild(chartDiv);
            modalContent.appendChild(closeButton);
            modal.appendChild(modalContent);
            document.body.appendChild(modal);

            try {
                const breakdownChart = new ApexCharts(chartDiv, breakdownOptions);
                breakdownChart.render();
            } catch (error) {
                console.error('Error rendering breakdown chart:', error);
                chartDiv.innerHTML =
                    '<p class="text-danger">Error: Unable to render symptom breakdown. Please check the console for more details.</p>';
            }
        }

        // Event listener for filter changes
        document.querySelectorAll('#year-select, #month-select, #poe-select').forEach(select => {
            select.addEventListener('change', function() {
                const year = document.getElementById('year-select').value;
                const month = document.getElementById('month-select').value;
                const poe = document.getElementById('poe-select').value;
                window.location.href =
                    `{{ route('SymptomDiseaseAnalysis') }}?year=${year}&month=${month}&poeid=${poe}`;
            });
        });
    });
</script>
