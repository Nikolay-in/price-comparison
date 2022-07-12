$(document).ready(function(){
    $.ajax({
        url: "includes/charts.php?type=vendorsOffers",
        method: "GET",
        success: function(data) {
            var data = JSON.parse(data);
            var vendors = [];
            var offers = [];

            for(var i in data) {
                vendors.push(data[i].vendor);
                offers.push(data[i].offers);
            }

            var ctx = $("#vendorsOffers");

            var colorset = [];
            for (i = 0; i < vendors.length; i++) {
                var letters = '0123456789ABCDEF'.split('');
                color = '#';
                for (var j = 0; j < 6; j++ ) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                colorset.push(color);
            }

            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: vendors,
                  datasets: [{
                    data: offers,
                    backgroundColor: colorset,
                  }],
                },
                options: {
                    aspectRatio: 1,
                    maintainAspectRatio: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Vendors Offers'
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