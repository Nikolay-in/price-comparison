$(document).ready(function(){
    let searchParams = new URLSearchParams(window.location.href)
    let id = searchParams.get('id');
    $.ajax({
        url: "/includes/charts.php?type=priceHistory&id=" + id,
        method: "GET",
        success: function(data) {
            data = JSON.parse(data);
            let date = Object.keys(data);
            let minPrices = [];
            let maxPrices = [];
            let avg = [];

            for(let i of Object.values(data)) {
                avg.push(i.avg);
                minPrices.push(Math.min(...Object.values(i.prices).map(Number)));
                maxPrices.push(Math.max(...Object.values(i.prices).map(Number)));
            }
            
            let chartdata = {
                labels: date,
                datasets : [
                    {
                        label: 'Max Price',
                        lineTension: 0.3,
                        backgroundColor: "rgba(220, 53, 69,0.2)",
                        borderColor: "rgba(220, 53, 69,1)",
                        pointRadius: 0,
                        pointBackgroundColor: "rgba(220, 53, 69,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(220, 53, 69,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: maxPrices
                    },
                    {
                        label: 'Average Price',
                        lineTension: 0.3,
                        backgroundColor: "rgba(2,117,216,0.2)",
                        borderColor: "rgba(2,117,216,1)",
                        pointRadius: 0,
                        pointBackgroundColor: "rgba(2,117,216,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(2,117,216,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: avg
                    },
                    {
                        label: 'Min Price',
                        lineTension: 0.3,
                        backgroundColor: "rgba(127,255,0,0.2)",
                        borderColor: "rgba(127,255,0,1)",
                        pointRadius: 0,
                        pointBackgroundColor: "rgba(127,255,0,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(127,255,0,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: minPrices
                    }                
                ]
            };

            let ctx = $("#priceHistory");

            let barGraph = new Chart(ctx, {
                type: 'line',
                data: chartdata,
                options: {
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: false,
                            text: 'Temporary Products'
                        }
                    },
                    scales: {
                        xAxes: {
                            time: {
                            unit: 'date'
                            },
                            grid: {
                            display: true
                            },
                            ticks: {
                            maxTicksLimit: 7,
                            maxRotation: 0,
                            minRotation: 0 
                            }
                        },
                        yAxes: {
                            ticks: {
                            min: 0,
                            max: 40000,
                            maxTicksLimit: 5,
                            callback: function(value, index, ticks) {
                                return value.toFixed(2) + ' лв.';
                            }
                            },
                            grid: {
                                color: "rgba(0, 0, 0, .125)"
                            },
                        }
                    }
                }
            });
        },
        error: function(data) {
            console.log(data + ":URL:" + url);
        }
    });
});