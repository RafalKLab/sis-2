// Adjust the font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Area Chart Example
function initializeTotalProfitAreaChart(labels, actualData) {
    var ctx = document.getElementById("yearUserProfitAreaChart");
    // Calculate the maximum value and minimum value in the data array
    var maxValue = Math.max.apply(null, actualData);
    var minValue = Math.min.apply(null, actualData);
    // Need to check for negative values to adjust the scale accordingly
    var scaleBottom = minValue < 0 ? minValue * 1.5 : 0;

    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: "Faktinis pelnas",
                lineTension: 0.3,
                backgroundColor: "rgba(2,117,216,0.2)", // Bootstrap primary color with transparency
                borderColor: "#0275d8", // Bootstrap primary color
                pointRadius: 5,
                pointBackgroundColor: "#0275d8",
                pointBorderColor: "#ffffff", // White for contrast
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#0275d8",
                pointHitRadius: 50,
                pointBorderWidth: 2,
                data: actualData,
            }],
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
                        // If negative values should start at their natural position, set beginAtZero to false
                        beginAtZero: !actualData.some(value => value < 0),
                        min: scaleBottom,
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
