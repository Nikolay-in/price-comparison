$(document).ready(function(){
    $(".barID").each(function() {
        var id = $(this).val();
        var barID = `#bar-${id}`;
        var crawling = $("#barCrawl-" + id).val();
        var assigned = $("#barAssigned-" + id).val();
        var total = $("#barTotal-" + id).val();
        var ctx = $(barID);
        var myBars = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Crawling", "Assigned", "Total"],
                datasets: [{
                data: [crawling, assigned, total],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                }],
            },
            options: {
            //indexAxis: 'y',
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    });
});