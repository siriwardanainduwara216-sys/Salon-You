document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    const alertBox     = document.getElementById('alert-box');
    const btnSubmit    = document.getElementById('btn-submit');
    const btnText      = document.getElementById('btn-text');
    const btnSpinner   = document.getElementById('btn-spinner');

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (alertBox) alertBox.style.display = 'none';
            if (btnSubmit) btnSubmit.disabled = true;
            if (btnText) btnText.textContent = 'Processing...';
            if (btnSpinner) btnSpinner.style.display = 'inline-block';

            const formData = new FormData(registerForm);

            fetch('ajax/register-process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Register Now';
                if (btnSpinner) btnSpinner.style.display = 'none';

                if (data.status === 'success') {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-success';
                        alertBox.textContent = data.message + ' (Demo OTP: ' + data.debug_otp + ')';
                        alertBox.style.display = 'block';
                    }

                    
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-danger';
                        alertBox.textContent = data.message;
                        alertBox.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Register Now';
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
// OTP Verification Form Handler
const otpForm = document.getElementById('otp-form');
if (otpForm) {
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const alertBox   = document.getElementById('alert-box');
        const btnSubmit  = document.getElementById('btn-submit');
        const btnText    = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');

        if (alertBox) alertBox.style.display = 'none';
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnText) btnText.textContent = 'Verifying...';
        if (btnSpinner) btnSpinner.style.display = 'inline-block';

        const formData = new FormData(otpForm);

        fetch('ajax/otp-process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (btnSubmit) btnSubmit.disabled = false;
            if (btnText) btnText.textContent = 'Verify Account';
            if (btnSpinner) btnSpinner.style.display = 'none';

            if (data.status === 'success') {
                if (alertBox) {
                    alertBox.className = 'alert-box alert-success';
                    alertBox.textContent = data.message;
                    alertBox.style.display = 'block';
                }

                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                if (alertBox) {
                    alertBox.className = 'alert-box alert-danger';
                    alertBox.textContent = data.message;
                    alertBox.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
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