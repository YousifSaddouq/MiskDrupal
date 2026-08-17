(function ($, Drupal, once) {
  Drupal.behaviors.featuredNewsControls = {
    attach(context) {
      once('featured-news-controls', '.news', context).forEach((section) => {
        const $section = $(section);

        $section.find('.news-prev').on('click', function () {
          const $slider = $section.find('.slick-initialized').first();

          if ($slider.length) {
            $slider.slick('slickPrev');
          }
        });

        $section.find('.news-next').on('click', function () {
          const $slider = $section.find('.slick-initialized').first();

          if ($slider.length) {
            $slider.slick('slickNext');
          }
        });
      });
    }
  };
})(jQuery, Drupal, once);