<script>
    window.addEventListener("load", (event) => {
        // Use the correct variable name from the controller
        const provinceDistribution = @json($provinceDistribution);

        // Extract province names and total screenings
        const provinces = provinceDistribution.map(item => item.province);
        const provinceTotals = provinceDistribution.map(item => item.total_screenings);

        const ctx = document.getElementById('provinceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: provinces,
                datasets: [{
                    label: 'Screenings by Province',
                    data: provinceTotals,
                    backgroundColor: [
                        'rgba(244, 67, 54, 0.8)', // Material Red
                        'rgba(33, 150, 243, 0.8)', // Material Blue
                        'rgba(76, 175, 80, 0.8)', // Material Green
                        'rgba(255, 235, 59, 0.8)', // Material Yellow
                        'rgba(156, 39, 176, 0.8)', // Material Purple
                        'rgba(255, 152, 0, 0.8)', // Material Orange
                        'rgba(3, 169, 244, 0.8)' // Material Light Blue
                    ],
                    borderColor: [
                        'rgba(244, 67, 54, 1)',
                        'rgba(33, 150, 243, 1)',
                        'rgba(76, 175, 80, 1)',
                        'rgba(255, 235, 59, 1)',
                        'rgba(156, 39, 176, 1)',
                        'rgba(255, 152, 0, 1)',
                        'rgba(3, 169, 244, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bar chart
                responsive: true,
                elements: {
                    bar: {
                        borderWidth: 1,
                        barThickness: 15,
                        maxBarThickness: 20,
                        categoryPercentage: 0.7,
                        barPercentage: 0.8
                    }
                },
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Screening Volume by Province'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Screenings'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Province'
                        }
                    }
                }
            }
        });
    });
</script>


<script>
    window.addEventListener("load", (event) => {
        const provinceData = @json($provinceDistribution);
        const provinces = provinceData.map(item => item.province);
        const provinceTotals = provinceData.map(item => item.total_screenings);

        const ctx = document.getElementById('provinceLineChart').getContext('2d');
        new Chart(ctx, {
            type: 'line', // Changed to line chart
            data: {
                labels: provinces,
                datasets: [{
                    label: 'Screenings by Province',
                    data: provinceTotals,
                    fill: false, // Line chart without fill
                    borderColor: 'rgba(33, 150, 243, 0.8)', // Material Blue
                    tension: 0.1, // Smooth the line
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Screening Volume by Province'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Province'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Screenings'
                        }
                    }
                }
            }
        });
    });
</script>
