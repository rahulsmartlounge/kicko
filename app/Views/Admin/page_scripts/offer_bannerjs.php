<script>
/*********************************/

function confirmDelete(theId) {
	console.log("Deleting ID:", theId);
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this banner ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX call to delete
            $.ajax({
                url: "<?php echo base_url('banner/delete'); ?>/" + theId,
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
/*************************************/
//Active and Inactive status
$(document).on('change', '.checkactive', function () {
    var id = $(this).val();
    var status = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: baseUrl + 'offer_banner/changeStatus',  // Replace with your actual URL
        type: 'POST',
        data: { id: id, status: status },
        success: function (response) {
            $('#messageBox')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .text('Status updated successfully!')
                .show();

            setTimeout(() => {
                $('#messageBox').fadeOut();
            }, 2000);
        },
        error: function () {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text('Failed to update status.')
                .show();
        }
    });
});

/************************************************/
var baseUrl = "<?= base_url() ?>";

$('#imageSubmit').click(function (e) {
    e.preventDefault();
    $('#imageSubmit').prop('disabled', true);

    var form = $('#banner_add')[0];
    var formData = new FormData(form);

    $.ajax({
        url: baseUrl + "offer_banner/save", // Route to your controller method
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            if (response.status == 1) {
                $('#messageBox')
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .text(response.msg)
                    .show();

                setTimeout(function () {
                    $('#imageSubmit').prop('disabled', false);
                    window.location.href = baseUrl + "offer_banner";
                }, 1000);
            } else {
                $('#messageBox')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text(response.msg)
                    .show();
                $('#imageSubmit').prop('disabled', false);
            }

            setTimeout(function () {
                $('#messageBox').hide();
            }, 3000);
        },
        error: function () {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text('Server error. Please try again.')
                .show();
            $('#imageSubmit').prop('disabled', false);
        }
    });
});

/*********************************/
$('#banner_image').on('change', function () {
    const [file] = this.files;
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    }
});
/***************************************/


$(document).ready(function () {
    var selectedSubId = "<?= isset($banner['the_SubId']) ? $banner['the_SubId'] : '' ?>";
    var selectedPrId = "<?= isset($banner['the_PrId']) ? $banner['the_PrId'] : '' ?>";

    var selectedCatId = $('#categoryName').val();

    if (selectedCatId) {
        $.ajax({
            url: baseUrl + "offer_banner/get-subcategories",
            type: "POST",
            data: { cat_id: selectedCatId },
            dataType: "json",
            success: function (response) {
                var subSelect = $('#subcategoryName');
                subSelect.empty().append('<option value="">-- Select Subcategory --</option>');

                $.each(response, function (i, item) {
                    subSelect.append('<option value="' + item.sub_Id + '" ' +
                        (item.sub_Id == selectedSubId ? 'selected' : '') + '>' + item.sub_Category_Name + '</option>');
                });

                if (selectedSubId) {
					$.ajax({
						url: baseUrl + "offer_banner/get-products",
						type: "POST",
						data: { sub_id: selectedSubId },
						dataType: "json",
						success: function (response) {
							var prSelect = $('#productName');
							prSelect.empty().append('<option value="">-- Select Product --</option>');

							$.each(response, function (i, item) {
								prSelect.append('<option value="' + item.pr_Id + '" ' +
									(item.pr_Id == selectedPrId ? 'selected' : '') + '>' + item.pr_Name + '</option>');
							});
						}
					});
				}

            }
        });
    }
});

$('#categoryName').on('change', function() {
    var categoryId = $(this).val();
    var subSelect = $('#subcategoryName');
    var messageElement = $('#noSubcategoryMsg');

    subSelect.empty(); // Clear old options

    if (categoryId) {
        $.ajax({
            url: baseUrl + "offer_banner/get-subcategories", // Make sure this route exists and works
            type: "POST",
            data: {
                cat_id: categoryId
            },
            dataType: "json",
            success: function(response) {
                if (response.length === 0) {
                    subSelect.append('<option value="">-- No Subcategory Available --</option>');
                    messageElement.show(); // Show the message
                } else {
                    subSelect.append('<option value="">-- Select Subcategory --</option>');
                    $.each(response, function(index, sub) {
                        subSelect.append('<option value="' + sub.sub_Id + '">' + sub
                            .sub_Category_Name + '</option>');
                    });
                  
                    messageElement.hide(); // Hide the message
                }
            },
            error: function(xhr) {
                console.error("Error fetching subcategories:", xhr.responseText);
            }
        });
    } else {
        subSelect.append('<option value="">-- Select Subcategory --</option>');
        messageElement.hide();
    }
});

/************************************************************/


// Load products when subcategory changes
$('#subcategoryName').on('change', function () {
    var categoryId = $('#categoryName').val(); // optional if needed in filtering
    var subcategoryId = $(this).val();
    var productSelect = $('#productName');
    var productMessage = $('#noproductMsg');

    productSelect.empty().append('<option value="">-- Select Product Name --</option>');
    productMessage.hide();

    if (subcategoryId) {
        $.ajax({
            url: baseUrl + "offer_banner/get-products",
            type: "POST",
            data: {
                cat_id: categoryId,   // optional, depends on your controller logic
                sub_id: subcategoryId
            },
            dataType: "json",
            success: function (response) {
                if (response.length === 0) {
                    productSelect.append('<option value="">-- No Products Available --</option>');
                    productMessage.show();
                } else {
                    $.each(response, function (index, product) {
                        productSelect.append('<option value="' + product.pr_Id + '">' + product.pr_Name + '</option>');
                    });
                }
            },
            error: function (xhr) {
                console.error("Error fetching products:", xhr.responseText);
            }
        });
    }
});

/***************************************/
$('#productList').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "<?= base_url('offer_banner/List') ?>",
        type: "POST",
        data: function (d) {
            d['<?= csrf_token() ?>'] = "<?= csrf_hash() ?>";
        }
    },
    columns: [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1; // Serial number
            },
            orderable: false,
            searchable: false
        },
        { data: 'the_Name' },
        { data: 'category_name' },
        { data: 'subcategory_name' },
        { data: 'product_name' },
        { data: 'the_Offer_Banner' },
        { data: 'status_switch' },
        { data: 'actions' }
    ],
    columnDefs: [
        { targets: [6,7], orderable: false, searchable: false },
        { targets: 5, render: function (data, type, row) {
            return data; // Render raw HTML for image thumbnail
        }}
    ]
});

</script>
