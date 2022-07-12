$(document).ready(function(){
    $.ajax({
        url: "includes/charts.php?type=activeOffers",
        method: "GET",
        success: function(data) {
            var data = JSON.parse(data);
            var dataset = [];

            for(var i in data) {
                dataset.push(data[i]);
            }

            var ctx = $("#activeOffers");

            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: ["Active for Active products", "Inactive", "Active for Inactive products"],
                  datasets: [{
                    data: dataset,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                  }],
                },
                options: {
                    maintainAspectRatio: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Offers Status'
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        },
        error: function(data) {
            console.log(data);
        }
    });
});