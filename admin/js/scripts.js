// Menu tooltip
(function () {
    'use strict'
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })
})()

$(document).ready(function () {

    // Toggle sidenav
    // Uncomment Below to persist sidebar toggle between refreshes
    // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
    //     document.body.classList.toggle('sb-sidenav-toggled');
    // }
    $('#sidebarToggle').click(() => {
        document.body.classList.toggle('sb-sidenav-toggled');
        localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
    });

    // Hide sidenav on outside click
    $('#layoutSidenav_content').click(function () {
        if ($('body').hasClass('sb-sidenav-toggled')) {
            $('body').toggleClass('sb-sidenav-toggled', false);
        }
    });


    //Pop Image
    $('.pop').on('click', function () {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
    });

    //Show image on hover
    var yOff = 15;
    var xOff = 20;
    var winWidth = $(window).width();
    var winHeight = $(window).height();
    var xCorr = 0;
    var yCorr = 0;
    $(".text-hover-image").hover(function (e) {
        var pathToImage = $(this).children("img").attr("alt");
        $("body").append("<div id='image-when-hovering-text'><img style=\"width: 384px; border: 1px solid;\" src='" + pathToImage + "'/></div>");
        let imgWidth = $('#image-when-hovering-text>img').width();
        let imgHeight = $('#image-when-hovering-text>img').height();
        if (winWidth - e.pageX < imgWidth) {
            xCorr = imgWidth + 2 * xOff;
        } else {
            xCorr = 0;
        }
        if (winHeight - (e.clientY - yOff) < imgHeight) {
            yCorr = imgHeight - 2 * yOff;
        } else {
            yCorr = 0;
        }
        $("#image-when-hovering-text")
            .css("position", "absolute")
            .css("top", (e.pageY - yOff - yCorr) + "px")
            .css("left", (e.pageX + xOff - xCorr) + "px")
            .fadeIn("fast");
    },

        function () {
            $("#image-when-hovering-text").remove();
        });

    $(".text-hover-image").mousemove(function (e) {
        let imgWidth = $('#image-when-hovering-text>img').width();
        let imgHeight = $('#image-when-hovering-text>img').height();
        if (winWidth - e.pageX < imgWidth) {
            xCorr = imgWidth + 2 * xOff;
        } else {
            xCorr = 0;
        }
        if (winHeight - (e.clientY - yOff) < imgHeight) {
            yCorr = imgHeight - 2 * yOff;
        } else {
            yCorr = 0;
        }
        $("#image-when-hovering-text")
            .css("top", (e.pageY - yOff - yCorr) + "px")
            .css("left", (e.pageX + xOff - xCorr) + "px");
    });
});