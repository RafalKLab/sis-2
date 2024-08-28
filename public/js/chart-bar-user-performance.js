// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Bar Chart Example
// Bar Chart Initialization Function
function initializeUserPerformanceBarChart(labels, data) {
    var ctx = document.getElementById("userPerformanceBarChart");
    var myLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Pelnas",
                backgroundColor: "rgb(54, 114, 247)",
                borderColor: "rgb(54, 114, 247)",
                data: data,
            }],
        },
        options: {
            scales: {
                xAxes: [{
                    // Adjust the following properties as needed
                    barPercentage: 0.5,
                    categoryPercentage: 0.5,
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        // Include a euro sign in the ticks
                        callback: function(value, index, values) {
                            return '€' + value;
                        },
                        maxTicksLimit: 5
                    },
                    gridLines: {
                        display: true
                    }
                }],
            },
            tooltips: {
                callbacks: {
                    // Include a euro sign in the tooltips
                    label: function(tooltipItem, chart) {
                        let label = chart.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += '€' + parseFloat(tooltipItem.value).toFixed(2); // This ensures the number is treated as a float and fixes to two decimals
                        return label;
                    }
                }
            },
            legend: {
                display: true,
                // Customize the legend label to include a euro sign
                labels: {
                    generateLabels: function(chart) {
                        var labels = Chart.defaults.global.legend.labels.generateLabels(chart);
                        labels[0].text += ' (€)';
                        return labels;
                    }
                }
            }
        }
    });
}
