$(document).ready(function(){
    $.ajax({
        url: "includes/charts.php?type=activeProducts",
        method: "GET",
        success: function(data) {
            var data = JSON.parse(data);
            var dataset = [];

            for(var i in data) {
                dataset.push(data[i]);
            }

            var ctx = $("#activeProducts");

            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: ["Assigned & Active", "Assigned & Inactive", "Unassigned & Inactive"],
                  datasets: [{
                    data: dataset,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                  }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Products Status'
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