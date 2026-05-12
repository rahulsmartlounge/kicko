<script>
//Data table
var baseUrl = "<?= base_url() ?>";
var csrfTokenName = "<?= csrf_token() ?>";
var csrfHash = "<?= csrf_hash() ?>";

$('#subcategoryList').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: baseUrl + "admin/subcategory/List",
        type: "POST",
        data: function(d) {
            d[csrfTokenName] = csrfHash;
        }
    },
    columns: [{
            data: null,
            render: function(data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1; // Serial number
            },
            orderable: false,
            searchable: false
        },
        {
            data: 'cat_Name'
        },
        {
            data: 'sub_Category_Name',
                render: function (data, type, row) {
                    return data.length > 20
                        ? '<span title="' + data + '">' + data.substring(0, 20) + '...</span>'
                        : data;
                }
        },
        {
            data: 'sub_Discount_Value',
            render: function(data, type, row) {
                return (data === null || data === undefined || data === '0' || data.trim() === '') ? 'N/A' : data;
            }
        },
        {
            data: 'sub_Discount_Type',
            render: function(data, type, row) {
                return (data === null || data === undefined || data.trim() === '') ? 'N/A' : data;
            }
        },
        {
            data: 'status_switch'
        },
        {
            data: 'actions'
        }
    ],
    columnDefs: [{
            targets: [5, 6],
            orderable: false,
            searchable: false
        },
        {
            targets: 5,
            render: function(data, type, row) {
                return data;
            }
        }
    ]
});
//Add Subcategory

var baseUrl = "<?= base_url() ?>";

$('#subcategorySubmit').click(function(e) {
    e.preventDefault();
    var url = baseUrl + "admin/subcategory/save";

    $.post(url, $('#createSubcategory').serialize(), function(response) {
        if (response.status == 1) {
            $('#messageBox')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .text(response.msg || 'Subcategory Created Successfully!')
                .show();

            setTimeout(function() {
                window.location.href = baseUrl + "admin/subcategory/";
            }, 3000);
        } else {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text(response.message || 'Please fill all the data')
                .show();
        }

        setTimeout(function() {
            $('#messageBox').empty().hide();
        }, 2000);
    }, 'json');
});


//Delete Sub category

function confirmDelete(subId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this SubCategory?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?php echo base_url('admin/subcategory/delete/'); ?>" + subId,
                method: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.msg, 'success');

                        let table = $('#subcategoryList').DataTable();
                        let currentPage = table.page();
                        table.ajax.reload(function() {
                            if (table.data().count() === 0 && currentPage > 0) {
                                table.page(currentPage - 1).draw(false);
                            }
                        }, false);
                    } else {
                        Swal.fire('Error', response.msg, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            });
        }
    });
}

//Active Inactive status Change
var baseUrl = "<?= base_url() ?>";

$(document).on('change', '.checkactive', function () {
    let subId = $(this).attr('id').split('-')[1]; // e.g., id="subcheck-5"
    let status = $(this).is(':checked') ? 1 : 2;

    $.ajax({
        url: baseUrl + 'admin/subcategory/status',
        type: 'POST',
        dataType: 'json',
        data: {
            sub_Id: subId,
            sub_Status: status
        },
        success: function (response) {
            if (response.success) {
                $('#messageBox')
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .text(response.message)
                    .show();
            } else {
                $('#messageBox')
                    //.removeClass('alert-success')
                    .addClass('alert-success')
                    .text(response.message)
                    .show();
            }
            $('html, body').animate({
                    scrollTop: 0
                }, 'fast');

            setTimeout(() => {
                $('#messageBox').fadeOut();
            }, 2000);
        },
        error: function (xhr, status, error) {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text('AJAX error: ' + error)
                .show();
        }
    });
});
</script>