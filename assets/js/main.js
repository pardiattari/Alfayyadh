document.addEventListener('DOMContentLoaded', function () {
    var thumbs = document.querySelectorAll('[data-gallery-thumb]');
    var mainImage = document.querySelector('[data-main-image]');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (mainImage) {
                mainImage.src = thumb.src;
            }
        });
    });

    var countdown = document.querySelector('[data-countdown]');
    if (countdown) {
        var boxes = countdown.querySelectorAll('span');
        var seconds = 23 * 3600 + 10 * 60 + 45;
        setInterval(function () {
            seconds = Math.max(0, seconds - 1);
            var hours = String(Math.floor(seconds / 3600)).padStart(2, '0');
            var minutes = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            var secs = String(seconds % 60).padStart(2, '0');
            if (boxes[0]) boxes[0].textContent = hours;
            if (boxes[1]) boxes[1].textContent = minutes;
            if (boxes[2]) boxes[2].textContent = secs;
        }, 1000);
    }

    document.querySelectorAll('[data-confirm]').forEach(function (item) {
        item.addEventListener('click', function (event) {
            if (!confirm(item.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });
});
