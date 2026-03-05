function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Press Enter to submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.login-form');
    const inputs = form.querySelectorAll('input');
    
    inputs.forEach((input, index) => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    form.submit();
                }
            }
        });
    });
});
