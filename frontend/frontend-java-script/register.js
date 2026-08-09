document.addEventListener('DOMContentLoaded', function() {
    

    //Password Show / Hide Toggle Function
    
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Type change according to type
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Eye Icon 
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
    // Register Form Submission (AJAX)
    
    const registerForm = document.getElementById('register-form');
    const alertBox     = document.getElementById('alert-box');
    const btnSubmit    = document.getElementById('btn-submit');
    const btnText      = document.getElementById('btn-text');
    const btnSpinner   = document.getElementById('btn-spinner');

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault(); // avoid page reloading

            // Loading state 
            if (alertBox) alertBox.style.display = 'none';
            if (btnSubmit) btnSubmit.disabled = true;
            if (btnText) btnText.textContent = 'Processing...';
            if (btnSpinner) btnSpinner.style.display = 'inline-block';
               const formData = new FormData(registerForm);
            // Backend Request 
            fetch('../backend/ajax/register-process.php', {
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
                // Submit Button Normal
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Register Now';
                if (btnSpinner) btnSpinner.style.display = 'none';

                if (data.status === 'success') {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-success';
                        alertBox.textContent = data.message + (data.debug_otp ? ' (Demo OTP: ' + data.debug_otp + ')' : '');
                        alertBox.style.display = 'block';
                    }

                    // redirect OTPVerifyPage 
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-danger';
                        alertBox.textContent = data.message || 'An error occurred.';
                        alertBox.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Server Response Error:', error);
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