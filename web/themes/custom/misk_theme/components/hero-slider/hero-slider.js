(function ($, Drupal, once) {
  if (typeof $.type === 'undefined') {
    $.type = function (obj) {
      if (obj === null) return 'null';
      if (obj === undefined) return 'undefined';
      return Object.prototype.toString.call(obj).slice(8, -1).toLowerCase();
    };
  }

  Drupal.behaviors.miskHeroSlider = {
    attach: function (context) {
      once('hero-slider', '.hero-slider', context).forEach(function (el) {
        $(el).slick({
          slide: '.slider',
          arrows: false,
          dots: true,
          customPaging: function (slider, i) {
            return '<button class="btn p-0"><span class="dot-arrow-icon"></span></button>';
          },
          dotsClass: 'slider-nav',
          fade: false,
          speed: 500,
          autoplay: true,
        });
        $('.slider-nav').appendTo('.slider-nav-wrapper .container');
      });
    }
  };
})(jQuery, Drupal, once);
