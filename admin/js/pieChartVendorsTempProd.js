$(document).ready(function(){
    $.ajax({
        url: "includes/charts.php?type=vendorsTempProducts",
        method: "GET",
        success: function(data) {
            var data = JSON.parse(data);
            var vendors = [];
            var tempProducts = [];

            for(var i in data) {
                vendors.push(data[i].vendor);
                tempProducts.push(data[i].tempProducts);
            }

            var ctx = $("#tempProductsVendors");

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
                type: 'doughnut',
                data: {
                  labels: vendors,
                  datasets: [{
                    data: tempProducts,
                    backgroundColor: colorset,
                  }],
                },
                options: {
                    //aspectRatio: 1,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Vendors Temp Products'
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