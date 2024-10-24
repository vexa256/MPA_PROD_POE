<script>
    window.addEventListener("load", (event) => {
        const poeData = @json($poeDistribution);

        // Extracting values for the chart from the poeData
        const types = poeData.map(item => item.type);
        const totals = poeData.map(item => item.total);

        // Creating the chart
        const ctx = document.getElementById('poeChart').getContext('2d');
        const poeChart = new Chart(ctx, {
            type: 'bar', // Horizontal bar chart
            data: {
                labels: types, // Labels (POE types)
                datasets: [{
                    label: 'Registered POEs',
                    data: totals, // Data points (counts)
                    backgroundColor: [
                        'rgba(0, 0, 139, 1)', // Customize as needed
                        'rgba(0, 128, 0, 1)',
                        'rgba(255, 140, 0, 1)'
                    ],
                    borderColor: [
                        'darkblue',
                        'green',
                        'orange'
                    ],
                    borderWidth: 1,
                    barThickness: 20,
                    categoryPercentage: 0.5,
                    barPercentage: 0.5,
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y', // This makes it a horizontal bar chart
                scales: {
                    y: {
                        beginAtZero: true
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
        const provinceTotals = provinceData.map(item => item.total);

        const ctx2 = document.getElementById('provinceChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: provinces,
                datasets: [{
                    label: 'POEs by Province',
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
                indexAxis: 'y', // This makes the bar chart horizontal
                responsive: true,
                elements: {
                    bar: {
                        borderWidth: 1,
                        barThickness: 15, // Thinner bar thickness
                        maxBarThickness: 20, // Prevent the bars from becoming too thick
                        categoryPercentage: 0.7, // Spacing between categories
                        barPercentage: 0.8 // Width of the bar in each category
                    }
                },
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Geographical Distribution of POEs by Province'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of POEs'
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
