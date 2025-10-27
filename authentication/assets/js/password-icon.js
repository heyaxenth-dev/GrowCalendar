document.addEventListener('DOMContentLoaded', function () {
	const togglePassword = document.getElementById('togglePassword');
	const passwordInput = document.getElementById('yourPassword');

	togglePassword.addEventListener('click', function (e) {
		e.preventDefault(); // prevent anchor from jumping
		const isHidden = passwordInput.type === 'password';
		passwordInput.type = isHidden ? 'text' : 'password';
		this.innerHTML = isHidden
			? '<i class="bi bi-eye-slash"></i>'
			: '<i class="bi bi-eye"></i>';
	});
});
