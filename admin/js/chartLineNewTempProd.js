$(document).ready(function(){
    $.ajax({
        url: "includes/charts.php?type=newTempProducts30days",
        method: "GET",
        success: function(data) {
            var data = JSON.parse(data);
            var date = [];
            var countNewTemp = [];
            var countTemp = [];

            for(var i in data) {
                //Format date
                formattedDate = data[i].date.split('-');
                formattedDate.shift();
                formattedDate = formattedDate[1] + '/' + formattedDate[0];

                date.push(formattedDate);
                countNewTemp.push(data[i].newTemp);
            }
            

            for(var i in data) {
                countTemp.push(data[i].temp);
            }
            var chartdata = {
                labels: date,
                datasets : [
                {
                    label: 'Modelled',
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: countTemp,
                    fill: {
                        target: 'origin',
                        above: 'rgba(2,117,216,0.2)',   // Area will be red above the origin
                        below: 'rgba(2,117,216,0.2)'    // And blue below the origin
                        }
                },
                {
                    label: 'Unmodelled',
                    lineTension: 0.3,
                    backgroundColor: "rgba(220, 53, 69,0.2)",
                    borderColor: "rgba(220, 53, 69,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(220, 53, 69,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(220, 53, 69,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: countNewTemp,
                    fill: {
                        target: 'origin',
                        above: 'rgba(220, 53, 69,0.2)',   // Area will be red above the origin
                        below: 'rgba(220, 53, 69,0.2)'    // And blue below the origin
                      }
                }
                
                ]
            };

            var ctx = $("#tempProducts");

            var barGraph = new Chart(ctx, {
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
                            gridLines: {
                            display: false
                            },
                            ticks: {
                                maxTicksLimit: 15,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        },
                        yAxes: {
                            ticks: {
                            min: 0,
                            max: 40000,
                            maxTicksLimit: 5
                            },
                            gridLines: {
                            color: "rgba(0, 0, 0, .125)",
                            }
                        },
                    },
                }
            });
        },
        error: function(data) {
            console.log(data);
        }
    });
});