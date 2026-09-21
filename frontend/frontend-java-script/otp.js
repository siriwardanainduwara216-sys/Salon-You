document.addEventListener('DOMContentLoaded', function() {
    
    
    // OTP Form Submission (AJAX)
    const otpForm    = document.getElementById('otp-form');
    const alertBox   = document.getElementById('alert-box');
    const btnSubmit  = document.getElementById('btn-submit');
    const btnText    = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');

    if (otpForm) {
        otpForm.addEventListener('submit', function(e) {
            e.preventDefault(); // avoid page reloading

            // Loading state display iin ui
            if (alertBox) alertBox.style.display = 'none';
            if (btnSubmit) btnSubmit.disabled = true;
            if (btnText) btnText.textContent = 'Verifying...';
            if (btnSpinner) btnSpinner.style.display = 'inline-block';

            const formData = new FormData(otpForm);

            fetch('otp-process.php', {
               method: 'POST',
              body: formData
           })
            .then(async response => {
                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (err) {
                    throw new Error(text);
                }
            })
            .then(data => {
                // Submit Button normal
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Verify Account';
                if (btnSpinner) btnSpinner.style.display = 'none';

                if (data.status === 'success') {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-success';
                        alertBox.textContent = data.message;
                        alertBox.style.display = 'block';
                    }

                    //after login redirect
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-danger';
                        alertBox.textContent = data.message || 'Verification failed.';
                        alertBox.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Server Response Error:', error);
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Verify Account';
                if (btnSpinner) btnSpinner.style.display = 'none';

                if (alertBox) {
                    alertBox.className = 'alert-box alert-danger';
                    alertBox.textContent = 'Server connection error. Please try again.';
                    alertBox.style.display = 'block';
                }
            });
        });
    }
});