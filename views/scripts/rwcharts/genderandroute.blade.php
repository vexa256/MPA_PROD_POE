<script>
    // Gender Distribution Chart
    var ctx = document.getElementById('genderDistributionChart').getContext('2d');
    var genderDistributionChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($genderCounts->keys()) !!},
            datasets: [{
                data: {!! json_encode($genderCounts->values()) !!},
                backgroundColor: ['#007bff', '#dc3545', '#ffc107'], // Adjust colors as needed
            }]
        },
        options: {
            responsive: true,
        }
    });

    // Genders Screened Per Province Chart
    var ctx2 = document.getElementById('gendersPerProvinceChart').getContext('2d');
    var gendersPerProvinceData = {!! json_encode($gendersScreenedPerProvince) !!};

    var provinces = Object.keys(gendersPerProvinceData);
    var genders = ['Male', 'Female', 'Other'];

    var datasets = genders.map(function(gender, index) {
        return {
            label: gender,
            data: provinces.map(function(province) {
                return gendersPerProvinceData[province][gender] || 0;
            }),
            backgroundColor: ['#007bff', '#dc3545', '#ffc107'][index],
        };
    });

    var gendersPerProvinceChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: provinces,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    beginAtZero: true,
                    stacked: true,
                }
            }
        }
    });
</script>
