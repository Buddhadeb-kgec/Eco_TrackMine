document.addEventListener('DOMContentLoaded', (event) => {
    const ctx = document.getElementById('emissionChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Diesel', 'Petrol', 'Coal', 'Electricity', 'Natural Gas', 'Fuel'],
            datasets: [{
                label: 'Consumption',
                data: [],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    fetch('../backend/submit data/fetch_data.php')
        .then(response => response.json())
        .then(data => {
            let aggregatedData = data.reduce((acc, current) => {
                acc[0] += parseFloat(current.diesel);
                acc[1] += parseFloat(current.petrol);
                acc[2] += parseFloat(current.coal);
                acc[3] += parseFloat(current.electricity);
                acc[4] += parseFloat(current.natural_gas);
                acc[5] += parseFloat(current.fuel);
                return acc;
            }, [0, 0, 0, 0, 0, 0]);

            chart.data.datasets[0].data = aggregatedData;
            chart.update();
        });
[_{{{CITATION{{{_1{](https://github.com/swadip-dutta/webdev/tree/5ab974b9566aa7b111631fd97607f7b36ab17f5d/resources%2Fviews%2Fbackend%2Fdashboard.blade.php)[_{{{CITATION{{{_2{](https://github.com/chetansingare/first-app/tree/70e988e15c20f68194bfddfbf07694427db430c0/src%2Fcomponents%2Fchart2%2Ftopseller%2FTopSellers.js)[_{{{CITATION{{{_3{](https://github.com/starand/scrm/tree/b5f52ad7da4a3d89a4a67572746d2055f494bb19/wallet.php)[_{{{CITATION{{{_4{](https://github.com/BUHUIQUMING-D/DYR/tree/90922d0bbd918f7a28f4334e8940e8f3405252db/%E8%80%83%E6%A0%B8%2FChart.js-master%2Fdocs%2Fdocs%2Fgetting-started%2Fusage.md)