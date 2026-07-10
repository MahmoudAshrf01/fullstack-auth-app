const forms = {
    login: document.getElementById('login-form'),
    register: document.getElementById('register-form'),
};

function showForm(targetId) {
    Object.values(forms).forEach((form) => {
        const isActive = form.id === targetId;
        form.hidden = !isActive;
        form.classList.toggle('active', isActive);
        if (!isActive) {
            clearFormErrors(form);
            form.reset();
        }
    });
}

function clearFormErrors(form) {
    form.querySelectorAll('.field-error').forEach((el) => (el.textContent = ''));
    form.querySelectorAll('.invalid').forEach((el) => el.classList.remove('invalid'));
}

function setFieldError(inputId, message) {
    const input = document.getElementById(inputId);
    const errorEl = document.getElementById(`${inputId}-error`);
    if (input) input.classList.add('invalid');
    if (errorEl) errorEl.textContent = message;
}

function validateLogin(form) {
    clearFormErrors(form);
    let valid = true;

    const email = form.querySelector('#login-email');
    const password = form.querySelector('#login-password');

    if (!email.value.trim()) {
        setFieldError('login-email', 'Email is required.');
        valid = false;
    } else if (!email.checkValidity()) {
        setFieldError('login-email', 'Enter a valid email address.');
        valid = false;
    }

    if (!password.value) {
        setFieldError('login-password', 'Password is required.');
        valid = false;
    }

    return valid;
}

function validateRegister(form) {
    clearFormErrors(form);
    let valid = true;

    const name = form.querySelector('#register-name');
    const email = form.querySelector('#register-email');
    const password = form.querySelector('#register-password');
    const confirm = form.querySelector('#register-confirm');
    const role = form.querySelector('#register-role');

    if (!name.value.trim() || name.value.trim().length < 2) {
        setFieldError('register-name', 'Name must be at least 2 characters.');
        valid = false;
    }

    if (!email.value.trim()) {
        setFieldError('register-email', 'Email is required.');
        valid = false;
    } else if (!email.checkValidity()) {
        setFieldError('register-email', 'Enter a valid email address.');
        valid = false;
    }

    if (!password.value || password.value.length < 6) {
        setFieldError('register-password', 'Password must be at least 6 characters.');
        valid = false;
    }

    if (confirm.value !== password.value) {
        setFieldError('register-confirm', 'Passwords do not match.');
        valid = false;
    }

    if (!role.value) {
        setFieldError('register-role', 'Please select a role.');
        valid = false;
    }

    return valid;
}

async function handleSubmit(form, validateFn, action) {
    if (!validateFn(form)) return;

    const btn = form.querySelector('.btn-primary');
    const btnText = btn.querySelector('.btn-text');
    const btnLoader = btn.querySelector('.btn-loader');

    btn.disabled = true;
    btnText.hidden = true;
    btnLoader.hidden = false;

    await new Promise((resolve) => setTimeout(resolve, 800));

    btn.disabled = false;
    btnText.hidden = false;
    btnLoader.hidden = true;

    console.log(`${action}:`, Object.fromEntries(new FormData(form)));
}

document.querySelectorAll('[data-show]').forEach((btn) => {
    btn.addEventListener('click', () => showForm(btn.dataset.show));
});

document.querySelectorAll('.toggle-password').forEach((btn) => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.classList.toggle('visible', isPassword);
        btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
    });
});

forms.login.addEventListener('submit', (e) => {
    e.preventDefault();
    handleSubmit(forms.login, validateLogin, 'Login');
});

forms.register.addEventListener('submit', (e) => {
    e.preventDefault();
    handleSubmit(forms.register, validateRegister, 'Register');
});
