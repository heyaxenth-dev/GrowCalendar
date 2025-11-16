document.addEventListener('DOMContentLoaded', () => {
	const passwordInput = document.getElementById('yourPassword');
	const wrapper = passwordInput.closest('.password-wrapper');
	const rules = wrapper.querySelector('.password-rules');
	const strengthBar = document.getElementById('passwordStrengthBar');
	const bar = strengthBar.querySelector('.progress-bar');

	passwordInput.addEventListener('focus', () => {
		rules.classList.remove('d-none');
		strengthBar.classList.remove('d-none');
	});

	passwordInput.addEventListener('input', () => {
		rules.classList.remove('d-none');
		strengthBar.classList.remove('d-none');
		validatePassword();
	});

	passwordInput.addEventListener('blur', () => {
		if (passwordInput.value === '') {
			rules.classList.add('d-none');
			strengthBar.classList.add('d-none');
		}
	});

	function validatePassword() {
		const value = passwordInput.value;

		const msgLength = wrapper.querySelector('.p-length');
		const msgUpper = wrapper.querySelector('.p-upper');
		const msgLower = wrapper.querySelector('.p-lower');
		const msgNumber = wrapper.querySelector('.p-number');
		const msgSpecial = wrapper.querySelector('.p-special');

		const hasLength = value.length >= 8;
		const hasUpper = /[A-Z]/.test(value);
		const hasLower = /[a-z]/.test(value);
		const hasNumber = /[0-9]/.test(value);
		const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(value);

		toggle(msgLength, hasLength);
		toggle(msgUpper, hasUpper);
		toggle(msgLower, hasLower);
		toggle(msgNumber, hasNumber);
		toggle(msgSpecial, hasSpecial);

		// Strength meter scoring
		let score = 0;
		if (hasLength) score++;
		if (hasUpper) score++;
		if (hasLower) score++;
		if (hasNumber) score++;
		if (hasSpecial) score++;

		updateStrengthBar(score);
	}

	function toggle(element, condition) {
		if (condition) {
			element.classList.add('valid');
			element.classList.remove('invalid');
		} else {
			element.classList.add('invalid');
			element.classList.remove('valid');
		}
	}

	function updateStrengthBar(score) {
		const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
		const classes = [
			'bg-danger',
			'bg-danger',
			'bg-warning',
			'bg-info',
			'bg-primary',
			'bg-success',
		];

		bar.style.width = widths[score];

		bar.classList.remove(
			'bg-danger',
			'bg-warning',
			'bg-info',
			'bg-primary',
			'bg-success'
		);
		bar.classList.add(classes[score]);
	}
});
