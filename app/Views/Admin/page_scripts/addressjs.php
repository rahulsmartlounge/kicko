<script>
function confirmDelete(addId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this customer address ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX call to delete
            $.ajax({
                url: "<?php echo base_url('admin/customer_address/delete'); ?>/" + addId,
                method: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.msg, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire('Error', response.msg, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            });
        }
    });
}
/*********************************************/

var baseUrl = "<?= base_url() ?>";

$('#custSubmit').click(function(e) {
$('#custSubmit').prop('disabled', true);
    e.preventDefault(); // Important to prevent normal form submit
    var url = baseUrl + "admin/customer_address/save"; // Correct route
    $.post(url, $('#createcustaddress').serialize(), function(response) {
		    $('html, body').animate({
            scrollTop: 0
        }, 'fast');
 
       // $('#createstaff')[0].reset();
        if (response.status == 1) { 
		$('#messageBox')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .text(response.msg || 'Customer address created successfully!')
                .show();
				 // Extract cust_id from the redirect URL
				let redirectUrl = response.redirect;
				let parts = redirectUrl.split('/');
				let cust_id = parts[parts.length - 1]; // "1"
            // Wait, then redirect
            setTimeout(function() {
				$('#custSubmit').prop('disabled', false);
				window.location.href = baseUrl + "admin/customer/location/" + cust_id;
            }, 3000);
        } 
		else {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text(response.msg || 'All mandatory fields are required')
                .show();
				$('#custSubmit').prop('disabled', false);
        }
		setTimeout(function() {
                $('#messageBox').empty().hide();
            }, 3000);
    }, 'json');
});

</script>
