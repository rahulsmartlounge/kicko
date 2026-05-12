<script>
$(document).ready(function () {
	const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phonePattern = /^\d{10}$/;

	$('#staffname').on('input', function () {
        const value = $(this).val().trim();
        $('#error-staffname').text(value ? '' : 'Name is required.');
    });
    $('#staffemail').on('input', function () {
        const value = $(this).val().trim();
        if (!value) {
            $('#error-staffemail').text('Primary email is required.');
        } else if (!emailPattern.test(value)) {
            $('#error-staffemail').text('Invalid email format.');
        } else {
            $('#error-staffemail').text('');
        }
    });
	$('#staffotemail').on('input', function () {
        const value = $(this).val().trim();
		if (!emailPattern.test(value)) {
            $('#error-staffotemail').text('Invalid email format.');
        }
		else {
			$('#error-staffotemail').text('');
		}		
    });
    $('#mobile').on('input', function () {
        const value = $(this).val().trim();
        if (!value) {
            $('#error-mobile').text('Phone number is required.');
        } else if (!phonePattern.test(value)) {
            $('#error-mobile').text('Phone number must be 10 digits.');
        } else {
            $('#error-mobile').text('');
        }
    });

    $('#password').on('input', function () {
        const value = $(this).val().trim();
        $('#error-password').text(value ? '' : 'Password is required.');
    });
});




var baseUrl = "<?= base_url() ?>";

$('#staffSubmit').click(function(e) {
	$('#staffSubmit').prop('disabled', true);
    e.preventDefault(); // Important to prevent normal form submit
    var url = baseUrl + "admin/save"; // Correct route

    $.post(url, $('#createstaff').serialize(), function(response) {
       // $('#createstaff')[0].reset();

        if (response.status == 1) { $('#messageBox')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .text(response.msg || 'Updated successfully!')
                .show();
        } 
		else {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text(response.msg || 'All mandatory fields are required')
                .show();
        }
		setTimeout(function() {
			$('#staffSubmit').prop('disabled', false);
                $('#messageBox').empty().hide();
            }, 3000);
    }, 'json');
});
</script>