document.addEventListener('DOMContentLoaded', function(){
    /**
     * jQuery(function($) {}); добавлено для избежания конфликта
     */
    jQuery(function($) {

        $('.our-teacher-wraper').slick({
            centerMode: true,
            slidesToShow: +Obj.slidesToShow,
            slidesToScroll: +Obj.slidesToScroll,
            autoplay: !!Obj.autoplay,
            autoplaySpeed: +Obj.autoplaySpeed,
            dots: false,
            dotsClass: 'slick-dots', // название класса для точек
            arrows: true,
            responsive: [
                {
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        initialSlide: 2
                    }
                },
                {
                    breakpoint: 750,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        initialSlide: 1
                    }
                },
                {
                    breakpoint: 450,
                    settings: {
                        centerMode: true,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        initialSlide: 2
                    }
                },
                {
                    breakpoint: 320,
                    settings: {
                        centerMode: true,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        initialSlide: 1
                    }
                }
            ]
        });
    });
});