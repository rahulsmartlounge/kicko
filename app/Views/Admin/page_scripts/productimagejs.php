<script>
$(document).ready(function() {
    $('#productList').DataTable({
        "processing": true,
        "serverSide": false,
        "searching": true,
        "paging": true,
        "ordering": true,
        "info": true,

    });
});

//Add product

var baseUrl = "<?= base_url() ?>";

$('#productimageSubmit').click(function(e) {
    e.preventDefault();

    var form = $('#createProductImage')[0];
    var formData = new FormData(form);
    // Append CSRF token manually
    formData.append("<?= csrf_token() ?>", "<?= csrf_hash() ?>");

    $.ajax({
        url: baseUrl + "productimage/save",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');

            if (response.status == 1) {
                $('#messageBox')
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .text(response.msg || 'Product created successfully!')
                    .show();
                setTimeout(function() {
                    window.location.href = baseUrl + "productimage/";
                }, 3000);
            } else {
                $('#messageBox')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text(response.msg || 'Please fill all the data.')
                    .show();
            }
            setTimeout(function() {
                $('#messageBox').empty().hide();
            }, 2000);
        }
    });
});

$('#media_files').on('change', function(event) {
    $('#imagePreview').empty(); 
    const files = event.target.files;

    if (files.length > 0) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const img = $('<img />', {
                        src: e.target.result,
                        class: 'img-thumbnail',
                        width: 100,
                        height: 100,
                        style: 'object-fit: cover;'
                    });
                    $('#imagePreview').append(img);
                };

                reader.readAsDataURL(file);
            }
        });
    }
});
</script>