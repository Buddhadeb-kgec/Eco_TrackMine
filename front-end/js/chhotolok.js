document.addEventListener('DOMContentLoaded', (event) => {
    const ctx = document.getElementById('consumptionChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Fuel', 'Petrol', 'Diesel', 'Electricity'],
            datasets: [{
                label: 'Consumption',
                data: [],
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
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

    window.submitData = function() {
        let fuel = document.getElementById('fuel').value;
        let petrol = document.getElementById('petrol').value;
        let diesel = document.getElementById('diesel').value;
        let electricity = document.getElementById('electricity').value;

        let data = [fuel, petrol, diesel, electricity];
        chart.data.datasets[0].data = data;
        chart.update();

        // Code to send data to your database goes here
    };
});
