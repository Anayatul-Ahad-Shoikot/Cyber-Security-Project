function getIP(callback) {
    fetch('https://api.ipify.org?format=json')
        .then(response => response.json())
        .then(data => callback(data.ip));
}

function checkIfBlocked(event) {
    event.preventDefault();

    getIP(ip => {
        fetch('../../backends/checking_blocked_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ip: ip })
        })
        .then(response => response.json())
        .then(data => {
            if (data.blocked) {
                showNotification('Your account has been blocked due to unusual activity.');
            } else {
                window.location.href = '../../pages/varification.php';
            }
        });
    });
}

function showNotification(message) {
    const notification = document.getElementById('notification2');
    notification.innerHTML = message;
    notification.style.right = '20px';
    setTimeout(() => {
        notification.style.right = '-540px';
    }, 5000);
}