document.addEventListener('DOMContentLoaded', () => {
	const passwordInput =
		document.getElementById('yourPassword') ||
		document.getElementById('password');
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
		validateConfirmPassword();
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

		// Return whether all rules are satisfied
		return hasLength && hasUpper && hasLower && hasNumber && hasSpecial;
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

	// Confirm Password Validation
	const confirmPasswordInput =
		document.getElementById('yourConfirmPassword') ||
		document.getElementById('confirm_password');

	if (confirmPasswordInput) {
		confirmPasswordInput.addEventListener('input', validateConfirmPassword);
		confirmPasswordInput.addEventListener('blur', validateConfirmPassword);
	}

	function validateConfirmPassword() {
		const confirmPasswordInput =
			document.getElementById('yourConfirmPassword') ||
			document.getElementById('confirm_password');

		if (!confirmPasswordInput) return false;

		const passwordValue = passwordInput.value;
		const confirmPasswordValue = confirmPasswordInput.value;

		const feedbackElement =
			confirmPasswordInput.parentElement.querySelector('.invalid-feedback') ||
			confirmPasswordInput.parentElement.parentElement.querySelector(
				'.invalid-feedback'
			);

		if (confirmPasswordValue === '') {
			confirmPasswordInput.classList.remove('is-valid');
			confirmPasswordInput.classList.remove('is-invalid');
			return false;
		} else if (passwordValue === confirmPasswordValue) {
			confirmPasswordInput.classList.remove('is-invalid');
			confirmPasswordInput.classList.add('is-valid');
			if (feedbackElement) {
				feedbackElement.textContent = 'Passwords match!';
				feedbackElement.style.display = 'none';
			}
			return true;
		} else {
			confirmPasswordInput.classList.remove('is-valid');
			confirmPasswordInput.classList.add('is-invalid');
			if (feedbackElement) {
				feedbackElement.textContent = 'Passwords do not match!';
				feedbackElement.style.display = 'block';
			}
			return false;
		}
	}

	// Prevent form submission unless password meets all rules and matches confirmation
	const form = passwordInput.closest('form');
	if (form) {
		form.addEventListener('submit', (event) => {
			const isPasswordValid = validatePassword();
			const isConfirmValid = validateConfirmPassword();

			// Trigger browser/Bootstrap validation styling
			form.classList.add('was-validated');

			if (!isPasswordValid || !isConfirmValid) {
				event.preventDefault();
				event.stopPropagation();
			}
		});
	}
});
