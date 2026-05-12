<script>
    var baseUrl = "<?= base_url() ?>";
    var csrfTokenName = "<?= csrf_token() ?>";
    var csrfHash = "<?= csrf_hash() ?>";

    $('#staffList').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: baseUrl + "admin/staff/List",
            type: "POST",
            data: function (d) {
                d[csrfTokenName] = csrfHash;
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
            {
                data: 'us_Name',
                render: function (data, type, row) {
                    return data && data.length > 20 ? data.substring(0, 20) + '...' : data;
                }
            },
            {
                data: 'us_Email'
            },
            {
                data: 'us_utype'
            },
            {
                data: 'us_Phone',
                render: function (data, type, row) {
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
        columnDefs: [
            {
                targets: [5, 6],
                orderable: false,
                searchable: false
            },
            {
                targets: 5,
                render: function (data, type, row) {
                    return data;
                }
            }
        ]
    }); 


    $(document).ready(function () {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phonePattern = /^\+?\d{7,15}$/;

        $('#staffname').on('input', function () {
            const value = $(this).val().trim();
            $('#error-staffname').text(value ? '' : 'Name is required.');
        });
        $('#staffemail').on('input', function () {
            const value = $(this).val().trim();
            if (!value) {
                $('#error-staffemail').text('Email is required.');
            } else if (!emailPattern.test(value)) {
                $('#error-staffemail').text('Invalid email format.');
            } else {
                $('#error-staffemail').text('');
            }
        });

        $('#staffotemail').on('input', function () {
            const value = $(this).val().trim();
            if (!value) {
                $('#error-staffotemail').text('Email field is required.');
            } else if (!emailPattern.test(value)) {
                $('#error-staffotemail').text('Invalid email format.');
            } else {
                $('#error-staffotemail').text('');
            }
        });
        $('#mobile').on('input', function () {
            let rawValue = $(this).val();

            // Remove all characters except digits and optional starting '+'
            rawValue = rawValue.replace(/[^\d+]/g, '');

            // Ensure '+' appears only at the beginning (if any)
            if (rawValue.indexOf('+') > 0) {
                rawValue = rawValue.replace(/\+/g, ''); // remove all '+' if not at start
            }

            // Set cleaned value back to input
            $(this).val(rawValue);

            const value = rawValue.replace(/\s+/g, '');

            if (!phonePattern.test(value)) {
                $('#error-mobile').text('Phone Number Must Be Minimum of 7 Digits.');
            } else {
                $('#error-mobile').text('');
            }
        });

    });




    var baseUrl = "<?= base_url() ?>";

    $('#staffSubmit').click(function (e) {
        let pwd = $('#password').val();
        let cpwd = $('#confirm_password').val();
        if (pwd !== cpwd) {
            $('#error-confirm-password').text('Passwords Do Not Match');
            return false;
        } else {
            $('#error-confirm-password').text('');
        }
        $('#staffSubmit').prop('disabled', true);
        e.preventDefault(); // Important to prevent normal form submit
        var url = baseUrl + "admin/staff/save"; // Correct route

        $.post(url, $('#createstaff').serialize(), function (response) {
            // $('#createstaff')[0].reset();

            if (response.status == 1) {
                $('#messageBox')
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .text(response.msg || 'Staff created Successfully!')
                    .show();


                // Wait, then redirect
                setTimeout(function () {
                    $('#staffSubmit').prop('disabled', false);
                    window.location.href = baseUrl +
                        "admin/staff/"; // Update this path to your Manage Staff page
                }, 300);
            } else {
                $('#messageBox')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text(response.msg || 'All mandatory fields are required')
                    .show();
                $('#staffSubmit').prop('disabled', false);

            }

            setTimeout(function () {

                $('#messageBox').empty().hide();
            }, 3000);
            $('html, body').animate({ scrollTop: 0 }, 'smooth'); // Scroll to top on error


        }, 'json');
    });

    /*********************************/

    function confirmDelete(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete this staff?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('admin/staff/delete'); ?>/" + userId,
                    method: "POST",
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.msg, 'success');
                            let table = $('#staffList').DataTable();
                            let currentPage = table.page();
                            table.ajax.reload(() => {
                                if (table.data().count() === 0 && currentPage > 0) {
                                    table.page(currentPage - 1).draw(false);
                                }
                            }, false);

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

    //  eye icon toggle password 

    function togglePassword(inputId, iconElement) {
        const input = document.getElementById(inputId);

        // Toggle input type
        if (input.type === "password") {
            input.type = "text";
            iconElement.classList.remove("fa-eye-slash");
            iconElement.classList.add("fa-eye");
        } else {
            input.type = "password";
            iconElement.classList.remove("fa-eye");
            iconElement.classList.add("fa-eye-slash");
        }
    }

    /*************************************/
    //Active and Inactive status
    var baseUrl = "<?= base_url() ?>";

    $(document).on('change', '.checkactive', function () {
        let usId = $(this).attr('id').split('-')[1]; // e.g., id="staffcheck-3"
        let status = $(this).is(':checked') ? 1 : 2;

        $.ajax({
            url: baseUrl + 'admin/staff/status',
            type: 'POST',
            dataType: 'json',
            data: {
                us_Id: usId,
                us_Status: status
            },
            success: function (response) {
                const messageBox = $('#messageBox');

                if (response.success) {
                    messageBox
                        .removeClass('alert-danger')
                        .addClass('alert-success')
                        .text(response.message)
                        .fadeIn();
                } else {
                    messageBox
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .text(response.message)
                        .fadeIn();
                }
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');


                setTimeout(() => {
                    messageBox.fadeOut();
                }, 3000);
            },
            error: function (xhr) {
                $('#messageBox')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text('Error Updating Status. Please Try Again Later.')
                    .fadeIn();

                setTimeout(() => {
                    $('#messageBox').fadeOut();
                }, 3000);

                console.error(xhr.responseText);
            }
        });
    });
</script>