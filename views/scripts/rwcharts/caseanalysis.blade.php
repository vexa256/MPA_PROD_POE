<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded');

        // Utility function to get CSS variable value
        function getCssVariableValue(variable) {
            return getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
        }

        // Chart 1: Disease Cases Bar Chart
        function initChart1(diseaseCounts) {
            console.log('Initializing Chart 1');
            console.log('Disease Counts:', diseaseCounts);

            const ctx = document.getElementById('kt_charts_widget_1_chart');
            if (!ctx) {
                console.error('Canvas element for chart 1 not found');
                return;
            }

            const labels = Object.keys(diseaseCounts);
            const data = Object.values(diseaseCounts);

            console.log('Labels:', labels);
            console.log('Data:', data);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cases',
                        data: data,
                        backgroundColor: getCssVariableValue('--bs-primary'),
                        borderColor: getCssVariableValue('--bs-primary'),
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + " cases";
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                color: getCssVariableValue('--bs-gray-200'),
                                borderDash: [4, 4]
                            }
                        }
                    }
                }
            });

            console.log('Chart 1 initialized');
        }

        // Chart 2: Monthly Trend Area Chart
        function initChart2(monthlyTrend, diseaseCounts) {
            console.log('Initializing Chart 2');
            console.log('Monthly Trend:', monthlyTrend);
            console.log('Disease Counts:', diseaseCounts);

            const ctx = document.getElementById('kt_charts_widget_2_chart');
            if (!ctx) {
                console.error('Canvas element for chart 2 not found');
                return;
            }

            const labels = Object.keys(monthlyTrend);
            const datasets = Object.keys(diseaseCounts).map((disease, index) => {
                const colors = ['#50CD89', '#F1416C', '#FFC700', '#7239EA', '#3F4254'];
                return {
                    label: disease,
                    data: labels.map(date => monthlyTrend[date][disease] || 0),
                    fill: true,
                    backgroundColor: `${colors[index % colors.length]}20`,
                    borderColor: colors[index % colors.length],
                    tension: 0.4
                };
            });

            console.log('Labels:', labels);
            console.log('Datasets:', datasets);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ": " + context.parsed.y +
                                        " cases";
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                color: getCssVariableValue('--bs-gray-200'),
                                borderDash: [4, 4]
                            }
                        }
                    }
                }
            });

            console.log('Chart 2 initialized');
        }

        // Initialize charts
        function initCharts(diseaseCounts, monthlyTrend) {
            console.log('Initializing charts');
            console.log('Disease Counts:', diseaseCounts);
            console.log('Monthly Trend:', monthlyTrend);

            if (!diseaseCounts || !monthlyTrend) {
                console.error('Chart data is missing or invalid');
                return;
            }

            initChart1(diseaseCounts);
            initChart2(monthlyTrend, diseaseCounts);
        }

        // Expose initCharts to global scope
        window.initCharts = initCharts;

        // Check if data is already available
        if (window.chartData) {
            console.log('Chart data found, initializing charts');
            initCharts(window.chartData.diseaseCounts, window.chartData.monthlyTrend);
        } else {
            console.log('Chart data not found');
        }
    });
</script>
