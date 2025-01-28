function exit() {
    fetch('../backends/logout.php')
        .then(response => {
            if (response.ok) {
                window.location.href = '../index.php';
            }
        });
}

window.onload = function() {
    const loadingScreen = document.getElementById('loading-screen');
    const thankYou = document.getElementById('thank-you');
    const randomTime = Math.floor(Math.random() * 4) + 5;

    setTimeout(() => {
        loadingScreen.style.display = 'none';
        thankYou.style.display = 'block';
    }, randomTime * 1000);
};