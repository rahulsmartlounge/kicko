<script>
$(document).ready(function () {
    var baseUrl = "<?= base_url() ?>";
    var csrfToken = "<?= csrf_token() ?>";
    var csrfHash = "<?= csrf_hash() ?>";

    $('#categoryList').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: baseUrl + "admin/category/List",
            type: "POST",
            data: function (d) {
                d[csrfToken] = csrfHash;
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                orderable: false,
                searchable: false
            },
            { 
                data: 'cat_Name',
                    render: function (data, type, row) {
                        return data.length > 20
                            ? '<span title="' + data + '">' + data.substring(0, 20) + '...' + '</span>'
                            : data;
                    }
            },
            { 
                data: 'cat_Discount_Value' ,
                render: function(data, type, row) {
                return (data === null || data === undefined || data === '0' || data.trim() === '') ? 'N/A' : data;
            }
            },
            { 
                data: 'cat_Discount_Type' ,
                render: function(data, type, row) {
                    return (data === null || data === undefined || data.trim() === '') ? 'N/A' : data;
                }
            },
            { data: 'status_switch' },
            { data: 'actions' }
        ],
        columnDefs: [
            {
                targets: [4, 5], 
                orderable: false,
                searchable: false
            },
            {
                targets: 4, 
                render: function (data, type, row) {
                    return data;
                }
            }
        ]
    });
});


//Add category

var baseUrl = "<?= base_url() ?>";
$('#categorySubmit').click(function(e) {
    e.preventDefault(); 
    var url = baseUrl + "admin/category/save"; 

    $.post(url, $('#createCategory').serialize(), function(response) {
        if (response.status == 1) {
            $('#messageBox')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .text(response.msg || 'Category Created Successfully!')
                .show();

            setTimeout(function() {
                window.location.href = baseUrl + "admin/category/"; 
            }, 3000);
        } else {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text(response.message || 'Please Fill all the Data')
                .show();
        }

        setTimeout(function() {
            $('#messageBox').empty().hide();
        }, 3000);
    }, 'json');
});

//Active and Inactive status
var baseUrl = "<?= base_url() ?>";

$(document).on('change', '.checkactive', function() {
    let catId = $(this).attr('id').split('-')[1]; // e.g., id="check-3" → prId=3
    let status = $(this).is(':checked') ? 1 : 2;

    $.ajax({
        url: baseUrl + 'admin/category/status', // Make sure route maps to controller
        type: 'POST',
        dataType: 'json',
        data: {
            cat_Id: catId,
            cat_Status: status
        },
        success: function(response) {
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
        error: function(xhr, status, error) {
            $('#messageBox')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text('AJAX error: ' + error)
                .show();
        }
    });
});
//Delete
 function confirmDelete(catId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this Category?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?php echo base_url('admin/category/delete/'); ?>" + catId,
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) {
                        Swal.fire('Deleted!', response.message, 'success');
                        let table = $('#categoryList').DataTable();
                        let currentPage = table.page();
                        table.ajax.reload(() => {
                            if (table.data().count() === 0 && currentPage !== 0) {
                                table.page(currentPage - 1).draw(false);
                            }
                        }, false);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
}

</script>