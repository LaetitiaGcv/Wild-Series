document.addEventListener('DOMContentLoaded', () => {
    let watch = document.querySelectorAll('.watchlist');

    for (let i = 0; i < watch.length; i++) {
        watch[i].addEventListener("click", (event) => {
            event.preventDefault();

            watch[i].classList.toggle('active');
            let link = watch[i].dataset.href;
            fetch(link)
                .then(function (res) {
                });
        });

    }
});