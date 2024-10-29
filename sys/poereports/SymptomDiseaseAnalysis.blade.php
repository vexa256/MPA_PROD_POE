<!--begin::Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">{{ $Title }}</span>
        </h3>
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body py-4">
        <!--begin::Alert-->
        <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.3"
                        d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z"
                        fill="currentColor" />
                    <path
                        d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z"
                        fill="currentColor" />
                </svg>
            </span>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-dark">{{ $Desc }}</h4>
            </div>
        </div>
        <!--end::Alert-->

        <!--begin::Form-->
        <form method="GET" action="{{ route('SymptomDiseaseAnalysis') }}" class="mb-15">
            <div class="row mb-6">
                <div class="col-md-6 fv-row">
                    <label for="year" class="fs-6 fw-semibold mb-2">Year</label>
                    <input type="number" class="form-control form-control-solid" placeholder="Enter Year"
                        id="year" name="year" value="{{ $selectedYear }}" />
                </div>
                <div class="col-md-6 fv-row">
                    <label for="month" class="fs-6 fw-semibold mb-2">Month</label>
                    <input type="number" class="form-control form-control-solid" placeholder="Enter Month"
                        id="month" name="month" value="{{ $selectedMonth }}" />
                </div>
                {{-- <div class="col-md-4 fv-row">
                    <label for="poeid" class="fs-6 fw-semibold mb-2">POE</label>
                    <select class="form-select form-select-solid" data-control="select2" data-placeholder="Select POE"
                        id="poeid" name="poeid">
                        <option value="">All</option>
                        @foreach ($pointsOfEntry as $poe)
                            <option value="{{ $poe->id }}" {{ $selectedPOEId == $poe->id ? 'selected' : '' }}>
                                {{ $poe->name }}</option>
                        @endforeach
                    </select>
                </div> --}}
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <span class="indicator-label">Filter</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </form>
        <!--end::Form-->

        <!--begin::Chart-->
        <div id="symptomDiseaseChart"></div>
        <!--end::Chart-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = @json($StackedBarData);
        const seriesData = [];
        const categories = [];

        // Extract categories (POEs) and series (diseases with symptoms and counts)
        Object.entries(data).forEach(([poeName, diseases]) => {
            categories.push(poeName);
            const poeSeries = Object.entries(diseases).map(([disease, symptoms]) => {
                return {
                    name: disease,
                    data: Object.keys(symptoms).map(symptom => ({
                        x: symptom,
                        y: symptoms[symptom]
                    }))
                };
            });
            seriesData.push(...poeSeries);
        });

        const options = {
            chart: {
                type: 'bar',
                stacked: true,
                height: 500,
                width: '100%',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            series: seriesData,
            xaxis: {
                categories: categories,
                title: {
                    text: 'Points of Entry (POE)'
                }
            },
            yaxis: {
                title: {
                    text: 'Symptom Count'
                }
            },
            title: {
                text: 'Common Reported Symptoms by Disease and POE',
                align: 'center'
            },
            tooltip: {
                y: {
                    formatter: val => `${val} case(s)`
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            }
        };

        const chart = new ApexCharts(document.querySelector("#symptomDiseaseChart"), options);
        chart.render();
    });
</script>
