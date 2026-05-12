<script>
    const phoneInput = document.querySelector("#phone");
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "in",
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
    });
 
    document.querySelector("#updateProfileForm").addEventListener("submit", function (e) {
        const fullNumber = iti.getNumber();
        phoneInput.value = fullNumber;
    });
  $(document).ready(function () {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
 
    // Profile Update Validation
	 $('#phone').on('input', function () {
        let value = $(this).val();
        let filtered = value.replace(/[^0-9\s\-]/g, '');
        $(this).val(filtered);
    });
$(document).ready(function () {
    $('#updateProfileForm').on('submit', function (e) {
        e.preventDefault();
 
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                let alertBox = $('#tog-alert');
 
                if (!alertBox.length) {
                    $('#updateProfileForm').before('<div class="alert" id="tog-alert"></div>');
                    alertBox = $('#tog-alert');
                }
 
                if (response.status === 1) {
                     $('#headerName').text(response.ad_name);
                    alertBox
                        .removeClass('alert-danger')
                        .addClass('alert-success')
                        .text(response.msg)
                        .fadeIn();
 
                    setTimeout(function () {
                        alertBox.fadeOut();
                    }, 3000);
                } else {
                    alertBox
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .text(response.msg)
                        .fadeIn();
                }
            },
            error: function () {
                alert('An Error Occurred While Updating The Profile.');
            }
        });
    });
});
 
 
    // Password Change Validation
  // $('#passUpdate').click(function(e) {
    // e.preventDefault();
    // var url = "<?= base_url('admin/profile/change_password') ?>";
    // $.post(url, $('#changePasswordForm').serialize(), function(data) {
        // if (data.status == 1) {
            // $('#passAlert').hide();
        // } else if (data.status == 0) {
            // $("#passAlert").html(data.msg).show();
            // setTimeout(function() {
                // $("#passAlert").fadeOut();
            // }, 1000); 
        // }
    // }, 'json');
// });
 
 
    // Show/Hide Password Toggle

 
document.querySelectorAll(".toggle-password").forEach(function(icon) {
    icon.addEventListener("click", function() {
        const targetId = this.getAttribute("data-target");
        const input = document.getElementById(targetId);
        const isPassword = input.getAttribute("type") === "password";
        input.setAttribute("type", isPassword ? "text" : "password");
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });
});
 
 
 
    // Auto-hide alert messages after 7 seconds
    setTimeout(function () {
      let alertEl = document.querySelector('#tog-alert');
      if (alertEl) {
        alertEl.classList.remove('show');
        alertEl.classList.add('fade');
        setTimeout(() => alertEl.remove(), 500);
      }
    }, 3000);
  });

  $(document).ready(function () {
    $('#passUpdate').on('click', function () {
        var formData = $('#changePasswordForm').serialize();
 
        $.ajax({
            url: "<?= base_url('admin/profile/change_password'); ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                var messageBox = $('#messageBox');
                messageBox.removeClass('alert-success alert-danger');
 
                if (response.status == 1) {
                    messageBox.addClass('alert alert-success').text(response.msg).fadeIn();
                    $('#changePasswordForm')[0].reset(); 
                } else {
                    messageBox.addClass('alert alert-danger').text(response.msg).fadeIn();
                }
 
                setTimeout(function () {
                    messageBox.fadeOut();
                }, 3000);
            }
        }); 
    }); 
}); 
</script>