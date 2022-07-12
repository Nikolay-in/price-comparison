// Initiate price graph and modal gallery
$(document).ready(() => {

    // Product image gallery modal
    $('.pop').on('click', function () {
        $('#imagemodal').modal('show');
    });


    // Initiate splide gallery
    var splide = new Splide('#main-carousel', {
        pagination: false,
        keyboard: 'global'
    });

    var thumbnails = document.getElementsByClassName('thumbnail');
    var current;

    for (var i = 0; i < thumbnails.length; i++) {
        initThumbnail(thumbnails[i], i);
    }

    function initThumbnail(thumbnail, index) {
        thumbnail.addEventListener('click', function () {
            splide.go(index);
        });
    }

    splide.on('mounted move', function () {
        var thumbnail = thumbnails[splide.index];

        if (thumbnail) {
            if (current) {
                current.classList.remove('is-active');
            }

            thumbnail.classList.add('is-active');
            current = thumbnail;
        }
    });
    splide.mount();


    //Initiate price history graph
    let id = $('#priceHistory').attr('data-id');
    $.ajax({
        url: "/includes/charts.php?type=priceHistory&id=" + id,
        method: "GET",
        success: function (data) {
            data = JSON.parse(data);
            let date = Object.keys(data).map(e => {
                e = e.split('-');
                e.pop();
                return e.join('/');
            });
            let minPrices = [];
            let maxPrices = [];
            let avg = [];

            for (let i of Object.values(data)) {
                avg.push(Number(i.avg).toFixed(2));
                minPrices.push(Math.min(...Object.values(i.prices).map(Number)).toFixed(2));
                maxPrices.push(Math.max(...Object.values(i.prices).map(Number)).toFixed(2));
            }

            let chartdata = {
                labels: date,
                datasets: [
                    {
                        label: 'Най-висока',
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
                        label: 'Средна',
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
                        label: 'Най-ниска',
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
            let ctx2 = $("#mobilePriceHistory");

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
                            text: 'Движение на цената'
                        },
                        tooltip: {
                            callbacks: {
                                label: (input) => (`${input.dataset.label}: ${new Intl.NumberFormat('en-GB', { style: 'decimal', minimumFractionDigits: 2 }).format(input.raw)} лв.`)
                            }
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
                                callback: function (value, index, ticks) {
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

            let barGraph2 = new Chart(ctx2, {
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
                            display: true,
                            text: 'Движение на цената'
                        },
                        tooltip: {
                            callbacks: {
                                label: (input) => (`${input.dataset.label}: ${new Intl.NumberFormat('en-GB', { style: 'decimal', minimumFractionDigits: 2 }).format(input.raw)} лв.`)
                            }
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
                                maxTicksLimit: 5,
                                maxRotation: 0,
                                minRotation: 0
                            }
                        },
                        yAxes: {
                            ticks: {
                                min: 0,
                                max: 40000,
                                maxTicksLimit: 5,
                                callback: function (value, index, ticks) {
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
        error: function (data) {
            console.log(data + ":URL:" + url);
        }
    });


    
    // Get aliexpress offers
    let ali = document.getElementById('ali');
    if (ali) {
        let title = document.getElementById('productTitle').textContent;
        let aliId = ali.value;

        const formData = new FormData()
        formData.append('catId', aliId);
        formData.append('title', title);

        fetch('/vendor/ae/getAliOffers.php', {
            method: 'post',
            body: formData
        })
            .then(res => res.json())
            .then(data => console.log(data));
    }
});