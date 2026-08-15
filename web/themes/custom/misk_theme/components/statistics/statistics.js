(function (Drupal, once) {

  function animateValue(obj, start = 0, end = null, duration = 3000) {
    if (!obj) {
      return;
    }

    const textStarting = obj.innerHTML;
    end = end || parseInt(textStarting.replace(/\D/g, ''));

    const range = end - start;
    const minTimer = 50;

    let stepTime = Math.abs(Math.floor(duration / range));
    stepTime = Math.max(stepTime, minTimer);

    const startTime = new Date().getTime();
    const endTime = startTime + duration;

    let timer;

    function run() {
      const now = new Date().getTime();
      const remaining = Math.max((endTime - now) / duration, 0);
      const value = Math.round(end - (remaining * range));

      obj.innerHTML = textStarting.replace(/([0-9]+)/g, value);

      if (value === end) {
        clearInterval(timer);
      }
    }

    timer = setInterval(run, stepTime);
    run();
  }

  Drupal.behaviors.miskStatistics = {
    attach(context) {

      once('misk-statistics', '.statistics-block', context).forEach(function (statisticsBlock) {

        const observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {

            if (entry.isIntersecting) {

              statisticsBlock.querySelectorAll('.value').forEach(function (el) {
                animateValue(el);
              });

              observer.unobserve(statisticsBlock);
            }

          });
        }, {
          threshold: 0.1
        });

        observer.observe(statisticsBlock);

      });

    }
  };

})(Drupal, once);