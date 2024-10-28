<!--begin::Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <h2 class="fw-bolder">{{ $Title }}</h2>
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <span class="text-muted fs-7 fw-bold me-2">{{ $Desc }}</span>
            </div>
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body py-4">
        <!--begin::Filters-->
        <div class="d-flex flex-stack flex-wrap mb-8">
            <div class="d-flex flex-wrap align-items-center">
                <!--begin::POE Filter-->
                <div class="w-200px me-4 my-2">
                    <select id="poe-filter" class="form-select form-select-solid" data-control="select2"
                        data-placeholder="Select POE">
                        <option value="">All POEs</option>
                        @foreach ($pointsOfEntry as $poe)
                            <option value="{{ $poe->id }}" {{ $selectedPOEId == $poe->id ? 'selected' : '' }}>
                                {{ $poe->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!--end::POE Filter-->

                <!--begin::Suspected Disease Filter-->
                <div class="w-200px me-4 my-2">
                    <select id="disease-filter" class="form-select form-select-solid" data-control="select2"
                        data-placeholder="Select Disease">
                        <option value="">All Diseases</option>
                        @foreach ($diseases as $disease)
                            <option value="{{ $disease }}" {{ $selectedDisease == $disease ? 'selected' : '' }}>
                                {{ $disease }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!--end::Suspected Disease Filter-->

                <!--begin::Month Filter-->
                <div class="w-200px me-4 my-2">
                    <select id="month-filter" class="form-select form-select-solid" data-control="select2"
                        data-placeholder="Select Month">
                        <option value="">All Months</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                {{ date('M', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <!--end::Month Filter-->

                <!--begin::Year Filter-->
                <div class="w-200px me-4 my-2">
                    <select id="year-filter" class="form-select form-select-solid" data-control="select2"
                        data-placeholder="Select Year">
                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $selectedYear == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <!--end::Year Filter-->
            </div>

            <!--begin::Apply Filters Button-->
            <div class="d-flex align-items-center my-2">
                <button type="button" id="apply-filters" class="btn btn-primary">Apply Filters</button>
            </div>
            <!--end::Apply Filters Button-->
        </div>
        <!--end::Filters-->

        <!--begin::Chart-->
        <div id="kt_charts_widget_1_chart" style="height: 350px;"></div>
        <!--end::Chart-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->

<!-- ApexCharts Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load chart data from controller
        var chartData = @json($alertData);

        // Prepare month labels for the entire year
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const highRiskAlertData = Array(12).fill(0); // Initialize with zero for each month

        // Populate highRiskAlertData based on chartData from the backend
        chartData.forEach(item => {
            const monthIndex = item.month - 1; // Convert month to array index
            highRiskAlertData[monthIndex] = item.high_risk_alert_count;
        });

        // Configure the ApexChart instance
        var options = {
            series: [{
                name: 'High-Risk Alert Volume',
                type: 'column',
                data: highRiskAlertData
            }, {
                name: 'Trend',
                type: 'line',
                data: highRiskAlertData
            }],
            chart: {
                height: 350,
                type: 'line',
            },
            stroke: {
                width: [0, 4]
            },
            title: {
                text: 'Monthly High-Risk Alerts Volume by POE'
            },
            labels: monthNames,
            xaxis: {
                type: 'category',
                title: {
                    text: 'Month'
                }
            },
            yaxis: [{
                title: {
                    text: 'High-Risk Alert Volume'
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#kt_charts_widget_1_chart"), options);
        chart.render();

        // Filter application functionality
        document.getElementById('apply-filters').addEventListener('click', function() {
            var url = new URL(window.location);
            url.searchParams.set('poe_id', document.getElementById('poe-filter').value);
            url.searchParams.set('disease', document.getElementById('disease-filter').value);
            url.searchParams.set('month', document.getElementById('month-filter').value);
            url.searchParams.set('year', document.getElementById('year-filter').value);
            window.location = url;
        });
    });
</script>



<style>
    /* Microsoft-inspired Custom Styles for AlertVolumeByMonth */

    :root {
        --ms-color-primary: #0078D4;
        --ms-color-primary-light: #50E6FF;
        --ms-color-secondary: #2B88D8;
        --ms-color-background: #F3F2F1;
        --ms-color-text: #323130;
        --ms-color-text-secondary: #605E5C;
        --ms-color-border: #EDEBE9;
        --ms-font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', 'Helvetica Neue', sans-serif;
    }

    body {
        font-family: var(--ms-font-family);
        background-color: var(--ms-color-background);
        color: var(--ms-color-text);
    }

    .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 4px;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid var(--ms-color-border);
    }

    .card-title h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--ms-color-primary);
    }

    .text-muted {
        color: var(--ms-color-text-secondary) !important;
    }

    .form-select {
        border-color: var(--ms-color-border);
        color: var(--ms-color-text);
        font-size: 14px;
    }

    .form-select:focus {
        border-color: var(--ms-color-primary);
        box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
    }

    .btn-primary {
        background-color: var(--ms-color-primary);
        border-color: var(--ms-color-primary);
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: var(--ms-color-secondary);
        border-color: var(--ms-color-secondary);
    }

    #kt_charts_widget_1_chart {
        background-color: #fff;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 16px;
    }

    /* Custom styles for Select2 dropdowns */
    .select2-container--default .select2-selection--single {
        border-color: var(--ms-color-border);
        height: 38px;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--ms-color-primary);
    }

    /* Custom styles for the chart */
    #kt_charts_widget_1_chart canvas {
        max-width: 100%;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .card-title h2 {
            font-size: 20px;
        }

        .w-200px {
            width: 100% !important;
            margin-right: 0 !important;
        }

        #kt_charts_widget_1_chart {
            height: 300px !important;
        }
    }

    /* Premium touch: subtle hover effects */
    .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease-in-out;
    }

    .btn-primary:focus {
        box-shadow: 0 0 0 3px rgba(0, 120, 212, 0.3);
    }

    /* Enterprise-grade data table styles */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th,
    .table td {
        border-top: none;
        border-bottom: 1px solid var(--ms-color-border);
        padding: 12px 16px;
        font-size: 14px;
    }

    .table thead th {
        background-color: #F8F8F8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--ms-color-text-secondary);
    }

    .table tbody tr:hover {
        background-color: #F0F8FF;
    }

    /* Accessibility improvements */
    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Focus styles for better keyboard navigation */
    a:focus,
    button:focus,
    input:focus,
    select:focus {
        outline: 2px solid var(--ms-color-primary);
        outline-offset: 2px;
    }

    /* Premium loading indicator */
    .loading-indicator {
        display: inline-block;
        width: 24px;
        height: 24px;
        border: 3px solid rgba(0, 120, 212, 0.3);
        border-radius: 50%;
        border-top-color: var(--ms-color-primary);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* High-contrast mode styles */
    @media (forced-colors: active) {
        .btn-primary {
            border: 2px solid ButtonText;
        }

        .form-select,
        .select2-container--default .select2-selection--single {
            border: 1px solid ButtonText;
        }
    }
</style>
