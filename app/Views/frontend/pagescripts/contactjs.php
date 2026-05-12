<script>
	document.querySelector("#contactForm").addEventListener("submit", function (e) {
		e.preventDefault();

		const form = e.target;
		const formData = new FormData(form);

		const name = formData.get("fullname").trim();
		const phone = formData.get("contact_no").trim();
		const email = formData.get("email").trim();
		const message = formData.get("message").trim();
		const responseDiv = document.getElementById("formResponse");

		const nameRegex = /^[a-zA-Z\s]+$/;
		const phoneRegex = /^\d{7,20}$/;
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

		if (!nameRegex.test(name)) {
			showMessage("Name must contain only letters and spaces.", "danger");
			return;
		}
		const allowedCharsRegex = /^[0-9+\s()]*$/; // allow digits, space, +, (, )
		const digitCount = phone.replace(/[^0-9]/g, '').length;

		if (!allowedCharsRegex.test(phone) || digitCount < 7 || digitCount > 20) {
			showMessage("Phone Number Must Contain 7 to 15 Digits.", "danger");
			return;
		}
		if (!emailRegex.test(email)) {
			showMessage("Enter a valid email ID.", "danger");
			return;
		}

		if (message === '') {
			showMessage("Message cannot be empty.", "danger");
			return;
		}

		// Optional: Disable submit button for better UX
		const submitBtn = form.querySelector('button[type="submit"]');
		submitBtn.disabled = true;
		submitBtn.innerText = "Please wait...";

		// Submit via Fetch
		fetch("<?= base_url('contact/submit') ?>", {
			method: "POST",
			body: formData,
			headers: {
				"X-Requested-With": "XMLHttpRequest"
			}
		})
			.then(response => response.json())
			.then(data => {
				if (data.status === '1') {
					form.reset();
					showMessage(data.message, "success");
				} else {
					showMessage(data.message, "danger");
				}
			})
			.catch(error => {
				showMessage("Something went wrong. Please try again later.", "danger");
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerText = "Submit";
			});

		// Show message helper
		function showMessage(msg, type) {
			responseDiv.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
			window.scrollTo({ top: 0, behavior: "smooth" });
			setTimeout(() => {
				responseDiv.innerHTML = "";
			}, 5000);
		}
	});
</script>