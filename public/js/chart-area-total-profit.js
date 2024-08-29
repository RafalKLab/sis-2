// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Area Chart Example
// Area Chart Example
function initializeTotalProfitAreaChart(labels, actualData, expectedData) {
    var ctx = document.getElementById("totalProfitAreaChart");
    // Calculate the maximum and minimum values in the data arrays
    var maxValue = Math.max(...actualData, ...expectedData);
    var minValue = Math.min(...actualData, ...expectedData);
    // Set maximum to be 50% higher than the maximum data value and minimum accordingly
    var adjustedMaxValue = maxValue * 1.5;
    var adjustedMinValue = minValue > 0 ? 0 : minValue * 1.5; // This ensures that if all values are positive, the min remains 0

    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: "Faktinis pelnas",
                lineTension: 0.3,
                backgroundColor: "rgba(50, 134, 87, 0.2)",
                borderColor: "#328657",
                pointRadius: 5,
                pointBackgroundColor: "#328657",
                pointBorderColor: "#ffffff",
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#328657",
                pointHitRadius: 50,
                pointBorderWidth: 2,
                data: actualData,
            },
                {
                    label: "Numatomas pelnas",
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "#0275d8",
                    pointRadius: 5,
                    pointBackgroundColor: "#0275d8",
                    pointBorderColor: "#ffffff",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#0275d8",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: expectedData,
                }
            ],
        },
        options: {
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false
                    },
                    ticks: {
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: adjustedMinValue,
                        max: adjustedMaxValue
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, .125)",
                    }
                }],
            },
            legend: {
                display: false
            }
        }
    });
}
