$(document).ready(function(){
    $.ajax({
        url: "includes/charts.php?type=offers30days",
        method: "GET",
        success: function(data) {
            var data = JSON.parse(data);
            var date = [];
            var count = [];

            for(var i in data) {
                //Format date
                formattedDate = data[i].date.split('-');
                formattedDate.shift();
                formattedDate = formattedDate[1] + '/' + formattedDate[0];

                date.push(formattedDate);
                count.push(data[i].count);
            }

            var chartdata = {
                labels: date,
                datasets : [
                {
                    label: 'Offers',
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
                    data: count,
                    fill: {
                        target: 'origin',
                        above: 'rgba(2,117,216,0.2)',   // Area will be red above the origin
                        below: 'rgba(2,117,216,0.2)'    // And blue below the origin
                      }
                }
                ]
            };

            var ctx = $("#offers30days");

            var barGraph = new Chart(ctx, {
                type: 'line',
                data: chartdata,
                options: {
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: true,
                            text: 'Approved Last 30 days'
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