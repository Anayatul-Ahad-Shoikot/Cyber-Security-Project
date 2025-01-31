document.addEventListener('DOMContentLoaded', function() {
    const nidInput = document.getElementById('nid-input');
    const verifyBtn = document.getElementById('next-btn');
    const notification = document.getElementById('notification');
    const timerDisplay = document.getElementById('timer');
    const loadingScreen = document.getElementById('loading-screen');
    let cooldownTimer = null;

    // Initial check for existing attempts
    checkAttemptStatus();

    async function checkAttemptStatus() {
        try {
            const response = await fetch('../../backend/check_attempts.php');
            const result = await response.json();
            
            if (result.blocked) {
                showNotification('Your device has been blocked due to multiple failed attempts.', Infinity);
                disableInputs();
            } else if (result.attempts > 0) {
                const remaining = Math.ceil((result.next_attempt - Date.now()) / 1000);
                if (remaining > 0) {
                    startTimer(remaining);
                    disableInputs();
                }
            }
        } catch (error) {
            console.error('Error checking attempts:', error);
        }
    }

    // Input validation
    nidInput.addEventListener('input', function() {
        const nid = this.value.trim();
        const isValid = /^\d{8,10}$/.test(nid);
        verifyBtn.disabled = !isValid;
    });

    // Submit handler
    verifyBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        const nid = nidInput.value.trim();

        loadingScreen.style.display = 'flex';
        await randomDelay(2000, 5000);

        try {
            const response = await fetch('../../backend/verify_nid.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nid })
            });

            const result = await response.json();

            if (result.status === 'voted') {
                showNotification('You have already cast your vote. Redirecting...', 5000);
                setTimeout(() => window.location.href = '../../index.php', 5000);
            } else if (result.status === 'valid') {
                window.location.href = '../../pages/casting.php';
            } else {
                handleInvalidAttempt(result);
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 3000);
        } finally {
            loadingScreen.style.display = 'none';
        }
    });

    async function handleInvalidAttempt(result) {
        if (result.attempts_remaining <= 0) {
            showNotification('Maximum attempts reached. Device blocked.', Infinity);
            disableInputs();
        } else {
            const remaining = Math.ceil((result.next_attempt - Date.now()) / 1000);
            startTimer(remaining);
            showNotification(`Invalid NID. Try again in <h2 id="timer">${remaining}</h2> seconds`, remaining * 1000);
            disableInputs();
        }
    }

    function startTimer(seconds) {
        clearInterval(cooldownTimer);
        let remaining = seconds;
        
        cooldownTimer = setInterval(() => {
            remaining--;
            timerDisplay.textContent = remaining;
            
            if (remaining <= 0) {
                clearInterval(cooldownTimer);
                enableInputs();
                checkAttemptStatus();
            }
        }, 1000);
    }

    function randomDelay(min, max) {
        return new Promise(resolve => {
            setTimeout(resolve, Math.floor(Math.random() * (max - min + 1)) + min);
        });
    }

    function showNotification(message, duration) {
        notification.innerHTML = message;
        notification.style.top = '20px';
        if (duration !== Infinity) {
            setTimeout(() => notification.style.display = 'none', duration);
        }
    }

    function disableInputs() {
        nidInput.disabled = true;
        verifyBtn.disabled = true;
        verifyBtn.classList.add('disabled');
    }

    function enableInputs() {
        nidInput.disabled = false;
        verifyBtn.disabled = false;
        verifyBtn.classList.remove('disabled');
    }
});