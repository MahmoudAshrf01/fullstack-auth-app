const forms = {
    login: document.getElementById('login-form'),
    register: document.getElementById('register-form'),
};

function showForm(targetId) {
    Object.values(forms).forEach((form) => {
        const isActive = form.id === targetId;
        form.hidden = !isActive;
        form.classList.toggle('active', isActive);
    });
}

document.querySelectorAll('[data-show]').forEach((btn) => {
    btn.addEventListener('click', () => showForm(btn.dataset.show));
});
