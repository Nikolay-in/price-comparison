// Initiate carousels
document.addEventListener( 'DOMContentLoaded', () => {
    document.querySelectorAll('.splide').forEach(carousel => new Splide( carousel, {
    type: 'loop',
    perPage: 5,
    perMove: 1,
    autoplay: true,
    lazyLoad: 'nearby',
    pauseOnFocus: false,
    interval: 9000,
    speed: 1500,
    pagination: false,
    easing: 'cubic-bezier(0.25, 0, 0.25, 1)',
    breakpoints: {
        1399: {
            perPage: 4
        },
        767: {
            perPage: 3
        },
        575: {
            perPage: 2
        }
    }
    } ).mount());
});