let invalidAttempts = 0;
let blockTime = 5000;

function getIP(callback) {
    fetch('https://api.ipify.org?format=json')
        .then(response => response.json())
        .then(data => callback(data.ip));
}

function validateInput() {
    const input = document.getElementById('nid-input').value;
    const nextBtn = document.getElementById('next-btn');
    if (input.length >= 8 && input.length <= 10) {
        nextBtn.disabled = false;
        nextBtn.classList.remove('disabled');
        nextBtn.classList.add('btn-secondary');
    } else {
        nextBtn.disabled = true;
        nextBtn.classList.add('disabled');
        nextBtn.classList.remove('btn-secondary');
    }
}

function verifyNID() {
    const input = document.getElementById('nid-input').value;
    const loadingScreen = document.getElementById('loading-screen');
    loadingScreen.style.display = 'flex';

    getIP(ip => {
        fetch('../../backends/verify_nid.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nid: input, ip: ip })
        })
            .then(response => {
                console.log('PHP file responded with status:', response.status);
                return response.json();
            })
            .then(data => {
                loadingScreen.style.display = 'none';
                if (data.casted) {
                    showNotification('You have already cast your vote. Redirecting to home page...');
                    setTimeout(() => {
                        window.location.href = '../../index.php';
                    }, 5000);
                } else {
                    if (data.success) {
                        deleteCookie(`timerExpiration_${ip}`);
                        deleteCookie(`invalidAttempts_${ip}`);
                        window.location.href = '../../pages/casting.php';
                    } else {
                        showNotification('Invalid NID. Please try Again after <h2 id="timer">5</h2> seconds.');
                        incrementInvalidAttempts(ip);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingScreen.style.display = 'none';
                showNotification('An error occurred. Please try again later.');
            });
    });
}

function showNotification(message) {
    const notification = document.getElementById('notification');
    notification.innerHTML = message;
    notification.style.right = '20px';
}

function startDelayedTimer(ip) {
    const notification = document.getElementById('notification');
    const timer = document.getElementById('timer');
    const input = document.getElementById('nid-input');
    const nextBtn = document.getElementById('next-btn');

    let timeLeft = 5;
    const expirationTime = Date.now() + timeLeft * 1000;
    document.cookie = `timerExpiration_${ip}=${expirationTime}; max-age=${timeLeft}; path=/`;

    timer.textContent = timeLeft;
    input.disabled = true;
    nextBtn.disabled = true;
    nextBtn.classList.add('disabled');
    nextBtn.classList.remove('btn-secondary');

    const countdown = setInterval(() => {
        timeLeft = Math.ceil((expirationTime - Date.now()) / 1000);
        timer.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(countdown);
            notification.style.right = '-405px';
            input.disabled = false;
            nextBtn.disabled = false;
            nextBtn.classList.remove('disabled');
            nextBtn.classList.add('btn-secondary');
            deleteCookie(`timerExpiration_${ip}`);
        }
    }, 1000);
}

function disableInput() {
    const input = document.getElementById('nid-input');
    input.disabled = true;
    setTimeout(() => {
        input.disabled = false;
    }, blockTime);
}

function blockUser(ip) {
    fetch('../../backends/blocked_devices.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ip: ip })
    });
    deleteCookie(`timerExpiration_${ip}`);
    deleteCookie(`invalidAttempts_${ip}`);
    showNotification('Your account has been blocked due to unusual activity.');

    setTimeout(() => {
        window.location.href = '../../index.php';
    }, 5000);
}

function deleteCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999;';
}

function incrementInvalidAttempts(ip) {
    fetch('../../backends/invalidAttempts.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ip: ip, invalidAttempts: invalidAttempts + 1 })
    })
    .then(response => response.text())
    .then(data => {
        invalidAttempts++;
        if (invalidAttempts >= 3) {
            blockUser(ip);
        } else {
            startDelayedTimer(ip);
        }
    })
    .catch(error => console.error('Error:', error));
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

window.onload = function () {
    getIP(ip => {
        fetch(`../../backends/getInvalidAttempts.php?ip=${ip}`)
            .then(response => response.text())
            .then(data => {
                invalidAttempts = parseInt(data) || 0;
                if (invalidAttempts >= 3) {
                    blockUser(ip);
                } else {
                    const timerExpirationCookie = document.cookie.split('; ').find(row => row.startsWith(`timerExpiration_${ip}=`));
                    if (timerExpirationCookie) {
                        const expirationTime = parseInt(timerExpirationCookie.split('=')[1]);
                        const timeLeft = Math.ceil((expirationTime - Date.now()) / 1000);
                        if (timeLeft > 0) {
                            const notification = document.getElementById('notification');
                            const timer = document.getElementById('timer');
                            const input = document.getElementById('nid-input');
                            const nextBtn = document.getElementById('next-btn');

                            timer.textContent = timeLeft;
                            notification.style.right = '20px';
                            input.disabled = true;
                            nextBtn.disabled = true;
                            nextBtn.classList.add('disabled');
                            nextBtn.classList.remove('btn-secondary');

                            const countdown = setInterval(() => {
                                const remainingTime = Math.ceil((expirationTime - Date.now()) / 1000);
                                timer.textContent = remainingTime;
                                if (remainingTime <= 0) {
                                    clearInterval(countdown);
                                    notification.style.right = '-405px';
                                    input.disabled = false;
                                    nextBtn.disabled = false;
                                    nextBtn.classList.remove('disabled');
                                    nextBtn.classList.add('btn-secondary');
                                    deleteCookie(`timerExpiration_${ip}`);
                                }
                            }, 1000);
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    });
};