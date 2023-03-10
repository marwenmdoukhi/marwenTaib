import 'nouislider';
const slider = document.getElementById('price-slider')

if (slider) {
    const min = document.getElementById('min')
    const max = document.getElementById('max')
    const minValue = Math.floor(parseInt(slider.dataset.min, 10) / 10) * 10
    const maxValue = Math.ceil(parseInt(slider.dataset.max, 10) / 10) * 10
    const range = noUiSlider.create(slider, {
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        step: 10,
        range: {
            'min': minValue,
            'max': maxValue
        }
    });

    var skipValues = [
        document.getElementById('value-lower'),
        document.getElementById('value-upper')
    ];

    range.on('slide', function (values, handle) {
        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })
    range.on('end', function (values, handle) {
        if (handle===0) {
            min.dispatchEvent(new Event('change'))
        } else {
            max.dispatchEvent(new Event('change'))
        }
    })
}

$(document).on("mousemove", ".category", function () {
    $(this).removeClass("sf-with-ul");
});

$('.btn').on('click', function(){
});
(function($) {
    $.fn.button = function(action) {
        if (action === 'loading' && this.data('loading-text')) {
            this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
        }
        if (action === 'reset' && this.data('original-text')) {
            this.html(this.data('original-text')).prop('disabled', false);
        }
    };
}(jQuery));

$(document).on('click','.mailComposeBtn', function (e) {
    $('.lescruds').html("<div class='spinner-border' role='status'> <span class='sr-only'>Loading...</span> </div>");
    e.preventDefault();
    let url_crud = $(this).attr("href");
    $('.lescruds').load(url_crud);
})


    $('.subcategory').owlCarousel({
        stagePadding: 10,
        margin:10,
        nav:true,
        navText: [
            "<i class='fa fa-caret-left'></i>",
            "<i class='fa fa-caret-right'></i>"
        ],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:5
            }
        }
    });

