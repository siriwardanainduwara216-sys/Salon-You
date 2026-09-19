document.addEventListener('DOMContentLoaded', function() {
    // Password Show / Hide Toggle Functionality
    
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
       //Login Form Submission (AJAX)
     const loginForm  = document.getElementById('login-form');
    const alertBox   = document.getElementById('alert-box');
    const btnSubmit  = document.getElementById('btn-submit');
    const btnText    = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // loding state display in ui
            if (alertBox) alertBox.style.display = 'none';
            if (btnSubmit) btnSubmit.disabled = true;
            if (btnText) btnText.textContent = 'Logging in...';
            if (btnSpinner) btnSpinner.style.display = 'inline-block';

            // form data
            const formData = new FormData(loginForm);

            // b request
            fetch('../backend/ajax/login-process.php', {
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
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Login';
                if (btnSpinner) btnSpinner.style.display = 'none';

                if (data.status === 'success') {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-success';
                        alertBox.textContent = data.message;
                        alertBox.style.display = 'block';
                    }

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert-box alert-danger';
                        alertBox.textContent = data.message;
                        alertBox.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Login Error:', error);
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Login';
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