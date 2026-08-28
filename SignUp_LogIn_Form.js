window.showRegisterStep = function(step) {
    const step1 = document.getElementById('register-step-1');
    const step2 = document.getElementById('register-step-2');
    const step3 = document.getElementById('register-step-3');
    const steps = [step1, step2, step3];

    steps.forEach((el) => {
        if (!el) return;
        el.classList.add('d-none');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
    });

    const activeStep = step === 1 ? step1 : step === 2 ? step2 : step3;
    if (activeStep) {
        activeStep.classList.remove('d-none');
        activeStep.style.display = 'block';
        activeStep.setAttribute('aria-hidden', 'false');
    }
};

document.addEventListener('DOMContentLoaded', () => 
{
    const container = document.querySelector('.container');
    const registerBtn = document.querySelector('.register-btn');
    const loginBtn = document.querySelector('.login-btn');
    const passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/;

    const showStep = (step) => 
    {
        if (window.showRegisterStep) 
        {
            window.showRegisterStep(step);
        }
    };

    document.querySelectorAll('[data-register-step]').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const targetStep = Number(btn.getAttribute('data-register-step'));

            if (targetStep === 2) {
                const user = document.querySelector('input[name="reg_user"]');
                const email = document.querySelector('input[name="reg_email"]');
                const password = document.getElementById('reg_pass');
                const confirmPassword = document.getElementById('reg_pass_confirm');

                if (!user || !email || !password || !confirmPassword) {
                    showStep(2);
                    return;
                }

                if (!user.value || !email.value || !password.value || !confirmPassword.value) {
                    alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                    return;
                }

                if (!passwordPattern.test(password.value)) {
                    alert('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร และต้องมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และตัวเลข');
                    password.focus();
                    return;
                }

                if (password.value !== confirmPassword.value) {
                    alert('รหัสผ่านยืนยันไม่ตรงกัน');
                    confirmPassword.focus();
                    return;
                }
            }

            if (targetStep === 3) {
                const regName = document.querySelector('input[name="reg_name"]');
                const regSurname = document.querySelector('input[name="reg_surname"]');
                const regAge = document.querySelector('input[name="reg_age"]');
                const regGender = document.querySelector('select[name="reg_gender"]');

                if (!regName || !regSurname || !regAge || !regGender) {
                    showStep(3);
                    return;
                }

                if (!regName.value || !regSurname.value || !regAge.value || !regGender.value) {
                    alert('กรุณากรอกข้อมูลส่วนตัวให้ครบถ้วน');
                    return;
                }
            }

            showStep(targetStep);
        });
    });

    if (registerBtn && container) {
        registerBtn.addEventListener('click', (event) => {
            event.preventDefault();
            container.classList.add('active');
            showStep(1);
        });
    }

    if (loginBtn && container) {
        loginBtn.addEventListener('click', (event) => {
            event.preventDefault();
            container.classList.remove('active');
            showStep(1);
        });
    }


    document.querySelectorAll('.password-toggle').forEach((toggle) => {
        const togglePassword = () => {
            const targetId = toggle.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const icon = toggle.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            } else {
                input.type = 'password';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            }
        };

        toggle.addEventListener('click', togglePassword);
        toggle.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                togglePassword();
            }
        });
    });
});