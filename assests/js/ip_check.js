document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('notification2');
    const continueBtn =document.querySelector('.btn-secondary');

    function updateUI(isBlocked){
        if(isBlocked){
            notification.innerHTML = 'Your device has been blocked due to unusual activities.';
            notification.style.top = '10px';
            continueBtn.classList.remove('btn-secondary');
            continueBtn.classList.add('disabled');
            continueBtn.removeAttribute('href');
        } else {
            notification.style.top = '-50px';
            continueBtn.classList.add('btn-secondary');
            continueBtn.classList.remove('disabled');
            continueBtn.setAttribute('href', './pages/varification.php');
        }
    }

    async function checkIP(){
        try{
            const ipResponse = await fetch('https://api.ipify.org?format=json');
            const ipData = await ipResponse.json();
            const ip = ipData.ip;

            const dbResponse = await fetch('../../backend/checking_blocked_ip.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ip: ip })
            });
            const result = await dbResponse.json();
            updateUI(result.blocked);
        } catch (error){
            console.error('Error checking IP status:', error);
            updateUI(false);
        }
    }

    checkIP();
});