document.addEventListener('DOMContentLoaded', () => {
	const emailInput = document.getElementById('yourEmail');
	const usernameInput = document.getElementById('yourUsername');

	emailInput.addEventListener('blur', () => {
		checkAvailability('email', emailInput.value, emailInput);
	});

	usernameInput.addEventListener('blur', () => {
		checkAvailability('username', usernameInput.value, usernameInput);
	});

	function checkAvailability(type, value, inputField) {
		if (value.trim() === '') return;

		fetch(
			'check_availability.php?type=' +
				type +
				'&value=' +
				encodeURIComponent(value)
		)
			.then((response) => response.json())
			.then((data) => {
				if (data.exists) {
					inputField.classList.add('is-invalid');
					inputField.classList.remove('is-valid');
				} else {
					inputField.classList.add('is-valid');
					inputField.classList.remove('is-invalid');
				}
			})
			.catch((error) => console.error('Error:', error));
	}
});
