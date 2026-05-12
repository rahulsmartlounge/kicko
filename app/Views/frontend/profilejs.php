<script>
    window.phoneInputs = {};
    $(document).ready(function () {
        $('.login-check').click(function (e) {
            const zd_uid = "<?= session()->get('zd_uid'); ?>";
            if (!zd_uid) {
                e.preventDefault(); // Stop navigation

                $('#modalBody').load("<?= base_url('weblogin'); ?>", function () {
                    $('#mainModal').modal('show');

                });
            }
        });
    });
document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;

    if (hash) {
        // Temporarily remove the ID to prevent native scroll
        const tabContent = document.querySelector(hash);
        let originalId = null;

        if (tabContent) {
            originalId = tabContent.id;
            tabContent.id = ''; // block scroll jump
        }

        // Scroll to top
        window.scrollTo(0, 0);

        // Delay tab activation slightly
        setTimeout(() => {
            if (originalId) {
                tabContent.id = originalId;
            }

            const tabButton = document.querySelector(`.nav-link[data-bs-target="${hash}"]`);
            if (tabButton) {
                new bootstrap.Tab(tabButton).show();
            }
        }, 50); // ⬅️ small delay allows layout to settle
    }

    // Replace URL hash without scroll
    document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('click', function () {
            const target = this.getAttribute('data-bs-target');
            if (history.replaceState) {
                history.replaceState(null, null, target);
            }
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {

        function initPhoneInput(selector) {

            const input = document.querySelector(selector);
            if (!input) return;

            const iti = window.intlTelInput(input, {
                initialCountry: "auto",
                geoIpLookup: function (callback) {
                    $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                        const countryCode = (resp && resp.country) ? resp.country : "IN";
                        callback(countryCode);
                    });
                },
                utilsScript: "js/utils.js"

            });

            input.dataset.original = input.value.trim(); // Save original for change check
            updatePhoneFormatHint(selector);
            input.addEventListener("countrychange", function () {
                // debugger;
                input.value = ""; // Clear number if flag changes
                updatePhoneFormatHint(selector);
            });

            input.addEventListener("input", function () {
                const errorDiv = document.querySelector(selector + "_error");
                const validDiv = document.querySelector(selector + "_valid");
                if (!input.value.trim()) {
                    errorDiv && (errorDiv.style.display = "none");
                    validDiv && (validDiv.style.display = "none");
                    return;
                }
                if (iti.isValidNumber()) {
                    errorDiv && (errorDiv.style.display = "none");
                    validDiv && (validDiv.style.display = "block");
                } else {
                    validDiv && (validDiv.style.display = "none");
                    if (errorDiv) {
                        errorDiv.textContent = "Invalid phone number.";
                        errorDiv.style.display = "block";
                    }
                }
            });

            window.phoneInputs[selector] = iti;
        }
        function updatePhoneFormatHint(selector) {
            const input = document.querySelector(selector);
            const iti = window.phoneInputs[selector];
            const formatDivId = selector + "_format";

            let formatDiv = document.querySelector(formatDivId);
            if (!formatDiv) {
                formatDiv = document.createElement("div");
                formatDiv.id = formatDivId;
                formatDiv.className = "phone-format-hint text-muted mt-1";
                input.parentNode.insertBefore(formatDiv, input.nextSibling);
            }

            if (iti && window.intlTelInputUtils) {
                const countryIso2 = iti.getSelectedCountryData().iso2;
                const exampleNumber = intlTelInputUtils.getExampleNumber(
                    countryIso2,
                    true,
                    intlTelInputUtils.numberFormat.INTERNATIONAL
                );

                // ✅ Mask logic: keep first 5 digits, rest as *
                const digits = exampleNumber.replace(/\D/g, '');
                let maskedExample = '';
                if (digits.length <= 5) {
                    maskedExample = digits;
                } else {
                    const visible = digits.substring(0, 5);
                    const masked = '*'.repeat(digits.length - 5);
                    maskedExample = visible + masked;
                }

                formatDiv.textContent = `Phone Number Format: ${maskedExample}`;
            } else {
                formatDiv.textContent = ''; // fallback
            }
        }


        function isPhoneValid(selector) {
            const input = document.querySelector(selector);
            const iti = window.phoneInputs[selector];
            const original = input.dataset.original || "";

            if (input.value.trim() === original) return true;
            return iti && iti.isValidNumber();
        }

        function appendPhoneData(formSelector, selector, codeField) {
            // debugger;
            const input = document.querySelector(selector);
            const iti = window.phoneInputs[selector];

            if (iti) {
                const number = iti.getNumber();
                const code = iti.getSelectedCountryData().dialCode;
                $(formSelector).append(`<input type="hidden" name="${codeField}" value="${code}">`);
                $(selector).val(number); // set formatted number
            }
        }

        // Initialize all phone inputs
        initPhoneInput("#phone");
        initPhoneInput("#newPhone");
        initPhoneInput("#add_Phone");

        // Export functions
        window.isPhoneValid = isPhoneValid;
        window.appendPhoneData = appendPhoneData;
    });

    function setPhoneNumberWhenReady(selector, phoneVal, retries = 10) {
        const input = document.querySelector(selector);

        if (!input) return;

        if (window.phoneInputs && phoneInputs[selector]) {
            const iti = phoneInputs[selector];

            if (phoneVal) {
                let rawNumber = phoneVal.trim();

                if (!rawNumber.startsWith('+')) {
                    const selectedCountry = iti.getSelectedCountryData();
                    const dialCode = selectedCountry?.dialCode || '91';
                    rawNumber = '+' + dialCode + rawNumber.replace(/^0+/, '');
                }

                iti.setNumber(rawNumber);
            } else {
                iti.setNumber('');
            }
        } else if (retries > 0) {
            // Retry after delay
            setTimeout(() => {
                setPhoneNumberWhenReady(selector, phoneVal, retries - 1);
            }, 200);
        } else {
            // Fallback
            $(selector).val(phoneVal || '');
        }
    }



    // Profile Form Submission
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();
        const phoneInput = $("#phone").val().trim();

        // ✅ Only validate if a phone number was entered
        if (phoneInput !== "" && !isPhoneValid("#phone")) {
            return;
        }

        // ✅ Only append phone if it's not empty
        if (phoneInput !== "") {
            appendPhoneData("#profileForm", "#phone", "cust_phcode");
        }
        $.ajax({
            type: 'POST',
            url: '<?= base_url('profile/editprofile') ?>',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                $('html, body').animate({ scrollTop: 0 }, 'fast');
                $('#messageBox')
                    .removeClass('alert-success alert-danger')
                    .addClass('alert-' + (response.status === 'success' ? 'success' : 'danger'))
                    .html(response.msg)
                    .fadeIn();
                if (response.status === 'success') {
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    setTimeout(() => $('#messageBox').fadeOut(), 5000);
                }
            },
            error: function () {
                $('#messageBox')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .html('An error occurred. Please try again.')
                    .fadeIn();
                setTimeout(() => $('#messageBox').fadeOut(), 5000);
            }
        });
    });

    function openAddAddressForm() {
        $('#addressFormContainer').show();
        $('#addressForm')[0].reset();
        $('#addressId').val('');

        // Smooth scroll to the form
        $('html, body').animate({
            scrollTop: $('#addressFormContainer').offset().top - 100 // adjust offset if needed
        }, 500); // 500ms duration
    }
    function discardAddressForm() {
        $('#addressFormContainer').hide();
        $('#addressForm')[0].reset();
        $('#addressId').val('');
    }
    $(document).ready(function () {

        $('#editAddressModal').on('shown.bs.modal', function (e) {
            const button = $(e.relatedTarget);
            const addId = button.data('add_id');
            if (!addId) return;
            // debugger;
            $.get("<?= base_url('profile/address/get') ?>/" + addId, function (res) {
                if (res.status === 'success' && res.data) {
                    const addr = res.data;

                    $('#add_Id').val(addr.add_Id);
                    $('#add_CustId').val(addr.add_CustId);
                    $('#add_Name').val(addr.add_Name);
                    $('#add_Email').val(addr.add_Email);
                    $('#add_BuldingNo').val(addr.add_BuldingNo);
                    $('#add_Street').val(addr.add_Street);
                    $('#add_Landmark').val(addr.add_Landmark);
                    $('#add_City').val(addr.add_City);
                    $('#add_State').val(addr.add_State);
                    $('#add_Pincode').val(addr.add_Pincode);
                    $('#is_default').prop('checked', addr.add_Default == 1);

                    const phoneSelector = '#add_Phone';
                    const phoneVal = addr.add_Phone?.trim() || '';
                    const input = document.querySelector(phoneSelector);

                    // Wait for intlTelInput to be ready
                    if (window.phoneInputs && window.phoneInputs[phoneSelector]) {
                        const iti = window.phoneInputs[phoneSelector];

                        if (phoneVal) {

                            let formatted = phoneVal;
                            if (!formatted.startsWith('+')) {
                                const selectedCountry = iti.getSelectedCountryData();
                                const dialCode = selectedCountry?.dialCode || '91';
                                formatted = '+' + dialCode + phoneVal.replace(/^0+/, '');
                            }
                            iti.setNumber(formatted);
                        } else {
                            iti.setNumber('');
                        }
                    } else {
                        $(phoneSelector).val(phoneVal);
                    }
                } else {
                    showMessage('Failed to load address details.', 'danger');
                }
            }, 'json');
        });




        // Address Form Submission (Add New)

$('#addressForm').on('submit', function (e) {
    e.preventDefault();

    const $submitBtn = $('#saveAddressBtn'); // assume this is your submit button ID
    if (!isPhoneValid("#newPhone")) return;

    appendPhoneData("#addressForm", "#newPhone", "new_phcode");

    // Prevent multiple clicks
    $submitBtn.prop('disabled', true).text('Saving...');

    const id = $('#addressId').val();
    const url = id ? 'profile/address/edit' : 'profile/address/add';

    $.post("<?= base_url() ?>" + url, $(this).serialize(), function (res) {
        if (res.status === 'success') {
            showMessage('Address Saved Successfully!', 'success');
            setTimeout(() => location.reload(), 3000);
        } else {
            showMessage(res.msg || 'Failed To Save Address.', 'danger');
            $submitBtn.prop('disabled', false).text('Save Address'); // Re-enable on failure
        }
    }, 'json').fail(function () {
        showMessage('Network error. Please try again.', 'danger');
        $submitBtn.prop('disabled', false).text('Save Address'); // Re-enable on failure
    });
});


        // Set tab and password toggle
        // let hash = window.location.hash;
        // if (hash) {
        //     $('.nav-link[href="' + hash + '"]').tab('show');
        // }
        // $('.nav-link[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        //     history.replaceState(null, null, e.target.hash);
        // });


//         document.addEventListener("DOMContentLoaded", function () {
//     const hash = window.location.hash;

//     if (hash) {
//         // Convert hash (e.g., "#address") to button with matching data-bs-target
//         const tabButton = document.querySelector(`.nav-link[data-bs-target="${hash}"]`);
//         if (tabButton) {
//             new bootstrap.Tab(tabButton).show();
//         }
//     }

//     // Optional: Update URL without scroll on tab click
//     document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(btn => {
//         btn.addEventListener('click', function () {
//             const target = this.getAttribute('data-bs-target');
//             if (history.replaceState) {
//                 history.replaceState(null, null, target);
//             } else {
//                 location.hash = target;
//             }
//         });
//     });
// });

        document.querySelectorAll(".toggle-password").forEach(function (icon) {
            icon.addEventListener("click", function () {
                const targetId = this.getAttribute("data-target");
                const input = document.getElementById(targetId);
                const isPassword = input.getAttribute("type") === "password";
                input.setAttribute("type", isPassword ? "text" : "password");
                this.classList.toggle("fa-eye");
                this.classList.toggle("fa-eye-slash");
            });
        });

        $('#changePasswordForm').on('submit', function (e) {
            e.preventDefault();
            const oldPassword = $('#oldPassword').val().trim();
            const newPassword = $('#newPassword').val().trim();
            const confirmPassword = $('#confirmPassword').val().trim();
            const messageBox = $('#messageBox');

            if (!oldPassword || !newPassword || !confirmPassword) {
                showMessage('All fields are required!', 'danger'); return;
            }
            if (newPassword.length < 6) {
                showMessage('New Password Must Be At Least 6 Characters Long!', 'danger'); return;
            }
            if (newPassword !== confirmPassword) {
                showMessage('New Password And Confirm Password Do Not Match!', 'danger'); return;
            }

            $.post("<?= base_url('profile/change_password') ?>", $(this).serialize(), function (response) {
                showMessage(response.msg, response.status ? 'success' : 'danger');
                if (response.status) $('#changePasswordForm')[0].reset();
            }, 'json');
        });

        function showMessage(msg, type = 'success') {
            const $box = $('#messageBox');
            $box
                .removeClass('alert-success alert-danger')
                .addClass('alert-' + type)
                .html(msg)
                .fadeIn();
            $('html, body').animate({ scrollTop: $box.offset().top - 20 }, 500);
            setTimeout(() => $box.fadeOut(), 3000);
        }
    });

    function editAddress(id) {
        $.post("<?= base_url('profile/getAddress') ?>", { add_Id: id }, function (res) {
            if (res.status === 'success') {
                const addr = res.data;
                // debugger;
                $('#add_Id').val(addr.add_Id);
                $('#add_CustId').val(addr.add_CustId);
                $('#add_Name').val(addr.add_Name);
                $('#add_Email').val(addr.add_Email);
                //$('#add_Phone').val(addr.add_Phone);
                $('#add_BuldingNo').val(addr.add_BuldingNo);
                $('#add_Street').val(addr.add_Street);
                $('#add_Landmark').val(addr.add_Landmark);
                $('#add_City').val(addr.add_City);
                $('#add_State').val(addr.add_State);
                $('#add_Pincode').val(addr.add_Pincode);
                $('#is_default').prop('checked', addr.add_Default == 1);


                $('#editAddressModal').modal('show');

                // Wait for modal to fully render, then set the phone number
                setTimeout(() => {
                    const phoneSelector = '#add_Phone';
                    const phoneVal = addr.add_Phone?.trim() || '';
                    if (window.phoneInputs && window.phoneInputs[phoneSelector]) {
                        const iti = window.phoneInputs[phoneSelector];
                        if (phoneVal && iti) {
                            iti.setNumber('+' + addr.add_phcode);
                            $('#add_Phone').val(
                                addr.add_Phone.replace(new RegExp("^\\+" + addr.add_phcode), '')
                            );

                        } else {
                            iti.setNumber('+91');
                            $('#add_Phone').val('');
                        }
                    } else {
                        $(phoneSelector).val(phoneVal); // fallback
                    }
                }, 50); // Less delay needed since input is pre-initialized
            } else {
                showMessage(res.msg || 'Failed To Load Address Data.', 'danger');
            }
        }, 'json');
    }

    // Helper function to wait for intlTelInput to be ready


$('#update_address').on('click', function (e) {
    e.preventDefault();

    const $form = $('#editAddressForm'); // ✅ get the form reference
    const $alertBox = $('#EditAddressModalAlert');

    if (!isPhoneValid("#add_Phone")) {
        $alertBox
            .removeClass('alert-success d-none')
            .addClass('alert-danger')
            .html('Please enter a valid phone number.')
            .fadeIn()
            .delay(4000)
            .fadeOut();
        return;
    }

    appendPhoneData("#editAddressForm", "#add_Phone", "add_phcode");
$.post("<?= base_url('profile/address/edit') ?>", $form.serialize(), function (res) {
    if (res.status === '1') {
        $alertBox
            .removeClass('alert-danger d-none')
            .addClass('alert-success')
            .html('Address Updated Successfully!')
            .fadeIn()
            .delay(2000)
            .fadeOut();

        const addr = res.updated_address;

        if (addr) {
            // Replace the address card HTML dynamically
            const html = `
                <div class="card p-3 h-100">
                    <strong>${addr.add_Name}</strong><br>
                    ${addr.add_BuldingNo}, ${addr.add_Street}<br>
                    ${addr.add_Landmark ? `${addr.add_Landmark}<br>` : ''}
                    ${addr.add_City}, ${addr.add_State} - ${addr.add_Pincode}<br>
                    Phone: ${addr.add_Phone} | Email: ${addr.add_Email}<br>
                    <div class="mt-2">
                        <a href="javascript:void(0)" onclick="editAddress(${addr.add_Id})">Edit</a> |
                        <a href="#" onclick="openDeleteModal(${addr.add_Id})">Remove</a>
                        ${addr.add_Default == 1
                            ? '| <span>Default</span>'
                            : `| <a href="javascript:void(0);" onclick="setDefaultAddress(${addr.add_Id})">Set as Default</a>`}
                    </div>
                </div>
            `;

            $(`#address_card_${addr.add_Id}`).html(html); // ✅ Replace only the updated address card
        }

        setTimeout(() => {
            $('#editAddressModal').modal('hide');
        }, 1500);
    } else {
        $alertBox
            .removeClass('alert-success d-none')
            .addClass('alert-danger')
            .html(res.msg || 'Failed To Update Address.')
            .fadeIn()
            .delay(4000)
            .fadeOut();
    }
}, 'json');

});

    $('#editAddressModal .btn-close').on('click', function () {
        const prId = $('#pr_Id').val()?.trim();
        const addId = $('#display_add_Id').val()?.trim();

        if (prId && addId) {
            // Clear the values to avoid redirecting again unintentionally
            $('#pr_Id').val('');
            $('#display_add_Id').val('');

            // Redirect to ordernow with address and product IDs
            window.location.href = "<?= base_url('ordernow/product') ?>/" + prId + "/" + addId;
        }
    });

    function openDeleteModal(id) {
        document.getElementById('delete_add_id').value = id;
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    function confirmDeleteAddress() {
        const addId = document.getElementById('delete_add_id').value;

        setTimeout(function () {
            $.ajax({
                url: '<?= base_url("profile/deleteAddress") ?>',
                type: 'POST',
                data: { add_Id: addId },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                        modal.hide();
                        window.location.href = "<?= base_url('profile#address') ?>";
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Something Went Wrong. Please Try Again.');
                }
            });
        }, 1000);
    }

    // Wait until page is fully loaded
    window.addEventListener('DOMContentLoaded', function () {
        const flashMsg = document.getElementById('flashMessage');
        if (flashMsg) {
            setTimeout(() => {
                flashMsg.style.display = 'none';
            }, 3000); // 3 seconds
        }
    });
    function setDefaultAddressOnClick(radio) {
        const id = $(radio).val();
        if ($(radio).is(':checked') && !$(radio).data('default')) {
            setDefaultAddress(id);
        }
    }

function setDefaultAddress(id) {
    $.post("<?= base_url('profile/setDefaultAddress') ?>", { add_Id: id }, function (res) {
        if (res.status === 'success') {
            showMessage('Default Address Updated.', 'success');

            // Step 1: Remove old default markers
            $('[id^="address_card_"]').each(function () {
                const $card = $(this);
                const $defaultLabel = $card.find('span:contains("Default")');
                const $setLink = $card.find('a:contains("Set as Default")');

                // Remove default label if exists
                if ($defaultLabel.length) {
                    $defaultLabel.remove();
                }

                // Restore "Set as Default" link if missing
                if (!$setLink.length) {
                    const cardId = $card.attr('id').replace('address_card_', '');
                    const actionContainer = $card.find('.mt-2');

                    if (actionContainer.length) {
                        actionContainer.append(`  <a href="javascript:void(0);" onclick="setDefaultAddress(${cardId})">Set as Default</a>`);
                    }
                }
            });

            // Step 2: Update selected address card
            const $currentCard = $('#address_card_' + id);
            $currentCard.find('a:contains("Set as Default")').remove();
            $currentCard.find('.mt-2').append('  <span>Default</span>');

        } else {
            showMessage(res.msg || 'Failed To Update Default Address.', 'danger');
        }
    }, 'json');
}


    function showMessage(msg, type = 'success') {
        const $box = $('#messageBox');
        $box
            .removeClass('alert-success alert-danger')
            .addClass('alert-' + type)
            .html(msg)
            .fadeIn();

        // Scroll to message box
        $('html, body').animate({
            scrollTop: $box.offset().top - 20
        }, 500);

        setTimeout(() => {
            $box.fadeOut();
        }, 3000);
    }

    $(document).ready(function () {
        const addId = sessionStorage.getItem('edit_address_id');
        const prId = sessionStorage.getItem('edit_product_id');

        if (addId || prId) {
            // Switch to address tab
            const addressTabTrigger = document.querySelector('#address-tab');
            if (addressTabTrigger) {
                const tab = new bootstrap.Tab(addressTabTrigger);
                tab.show();
            }

            // Load the address data
            setTimeout(() => {
                if (addId && typeof editAddress === 'function') {
                    editAddress(addId); // fill other fields
                    $('#display_add_Id').val(addId);                 // hidden input
                }

                if (prId) {
                    $('#pr_Id').val(prId);
                }

                // Open modal
                const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
                modal.show();
            }, 300);

            // Remove sessionStorage after everything is used
            sessionStorage.removeItem('edit_address_id');
            sessionStorage.removeItem('edit_product_id');
        }
    });
</script>