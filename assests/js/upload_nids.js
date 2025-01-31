document.addEventListener('DOMContentLoaded', () => {
    const uploadBtn = document.querySelector('.btn-secondary');
    uploadBtn.addEventListener('click', handleUpload);
});

async function handleUpload() {
    try {
        const fileInput = document.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        if (!file) {
            showNotification('Please select a file first', 'error');
            return;
        }
        const ip = await getIP();
        if (!isValidIP(ip)) {
            showNotification('Invalid IP address detected', 'error');
            return;
        }

        const fileContent = await readFileContent(file);

        const response = await fetch('../../backends/user_nid_upload.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ip: ip, nids: fileContent})
        });
        const result = await response.json();

        if (result.success) {
            showNotification('Data uploaded successfully', 'success');
            setTimeout(() => window.location.href = '../../pages/upload_user_data.php', 1500);
        } else {
            showNotification(result.error || 'Upload failed', 'error');
        }
        
    } catch (error) {
        console.error('Upload error:', error);
        showNotification('An error occurred during upload', 'error');
    }
}

async function getIP() {
    try {
        const response = await fetch('https://api.ipify.org?format=json');
        const data = await response.json();
        return data.ip;
    } catch (error) {
        throw new Error('IP detection failed');
    }
}

function isValidIP(ip) {
    return /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip);
}

async function readFileContent(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (event) => resolve(event.target.result);
        reader.onerror = (error) => reject(error);
        reader.readAsText(file);
    });
}

function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.style.top = '20px';
    
    setTimeout(() => {
        notification.style.top = '-200px';
    }, 5000);
}