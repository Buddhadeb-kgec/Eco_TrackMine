// Fetch data dynamically from backend
document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("emissionChart").getContext("2d");
  
    // Fetch data from backend API
    fetch("../backend/fetch_emission_data.php")
      .then((response) => response.json())
      .then((data) => {
        // Update chart dynamically
        const labels = data.labels;
        const chartData = data.values;
  
        const config = {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Carbon Emission (in tons)',
              data: chartData,
              backgroundColor: [
                'rgba(46, 204, 113, 0.7)',
                'rgba(52, 152, 219, 0.7)',
                'rgba(231, 76, 60, 0.7)',
                'rgba(241, 196, 15, 0.7)',
                'rgba(155, 89, 182, 0.7)',
              ],
              borderColor: [
                'rgba(46, 204, 113, 1)',
                'rgba(52, 152, 219, 1)',
                'rgba(231, 76, 60, 1)',
                'rgba(241, 196, 15, 1)',
                'rgba(155, 89, 182, 1)',
              ],
              borderWidth: 1,
            }],
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
                labels: {
                  color: '#ecf0f1',
                },
              },
              title: {
                display: true,
                text: 'Carbon Emissions by Fuel Type',
                color: '#ecf0f1',
                font: {
                  size: 18,
                },
              },
            },
            scales: {
              x: {
                ticks: {
                  color: '#ecf0f1',
                },
              },
              y: {
                ticks: {
                  color: '#ecf0f1',
                },
                beginAtZero: true,
              },
            },
          },
        };
  
        new Chart(ctx, config);
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  });
  