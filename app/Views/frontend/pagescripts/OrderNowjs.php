<!-- pagescripts/OrderNowjs.php -->
<script>
    const orderId = "<?= esc($od_Id) ?>";

    function initPhoneInput(selector, dialCodeSelector, formatSelector, validSelector, errorSelector) {
        const input = document.querySelector(selector);
        if (!input) return;

        const iti = window.intlTelInput(input, {
            nationalMode: false,
            initialCountry: "in",
            preferredCountries: ["in", "us", "gb"],
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18/build/js/utils.js"
        });

        window.phoneInputs = window.phoneInputs || {};
        window.phoneInputs[selector] = iti;

        input.addEventListener("countrychange", function () {
            if (dialCodeSelector) {
                document.querySelector(dialCodeSelector).value = "+" + iti.getSelectedCountryData().dialCode;
            }
            updatePhoneFormatHint(selector, formatSelector);
        });

        input.addEventListener("input", function () {
            if (iti.isValidNumber()) {
                if (validSelector) document.querySelector(validSelector).style.display = "block";
                if (errorSelector) document.querySelector(errorSelector).style.display = "none";
            } else {
                if (validSelector) document.querySelector(validSelector).style.display = "none";
                if (errorSelector) {
                    const err = document.querySelector(errorSelector);
                    err.style.display = "block";
                    err.textContent = "Invalid phone number";
                }
            }
        });

        if (dialCodeSelector) {
            document.querySelector(dialCodeSelector).value = "+" + iti.getSelectedCountryData().dialCode;
        }

        updatePhoneFormatHint(selector, formatSelector);
    }


    window.phoneInputs = window.phoneInputs || {};
    // initPhoneInput("#newPhone"); // already in your DOMContentLoaded

    document.addEventListener("DOMContentLoaded", function () {
        initPhoneInput("#newPhone", "#newphcode", "#phone_format", "#phone_valid", "#phone_error");
        initPhoneInput("#add_Phone", "#add_phcode", "#add_Phone_format", "#add_Phone_valid", "#add_Phone_error");

        const input = document.querySelector("#newPhone");
        const formatDiv = document.querySelector("#phone_format");

        if (!input || !formatDiv) return;

        const iti = window.intlTelInput(input, {
            initialCountry: "in",
            preferredCountries: ["in", "us", "ae"],
            nationalMode: false,
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18/build/js/utils.js"
        });

        // Store reference
        window.phoneInputs = window.phoneInputs || {};
        window.phoneInputs["#newPhone"] = iti;

        // Update hidden input on country change
        input.addEventListener("countrychange", function () {
            document.querySelector("#newphcode").value = "+" + iti.getSelectedCountryData().dialCode;
            updatePhoneFormatHint("#newPhone");
        });

        // Validate on input
        input.addEventListener("input", function () {
            if (iti.isValidNumber()) {
                document.querySelector("#phone_valid").style.display = "block";
                document.querySelector("#phone_error").style.display = "none";
            } else {
                document.querySelector("#phone_valid").style.display = "none";
                document.querySelector("#phone_error").style.display = "block";
                document.querySelector("#phone_error").innerText = "Invalid phone number";
            }
        });

        // Initial setup
        document.querySelector("#newphcode").value = "+" + iti.getSelectedCountryData().dialCode;
        updatePhoneFormatHint("#newPhone");
    });




    function updatePhoneFormatHint(selector, formatSelector) {
        const input = document.querySelector(selector);
        const iti = window.phoneInputs[selector];
        const formatDiv = document.querySelector(formatSelector);

        if (iti && window.intlTelInputUtils && formatDiv) {
            const iso2 = iti.getSelectedCountryData().iso2;
            const example = intlTelInputUtils.getExampleNumber(
                iso2,
                true,
                intlTelInputUtils.numberFormat.INTERNATIONAL
            );

            const digits = example.replace(/\D/g, '');
            let masked = digits.length <= 5 ? digits : digits.substring(0, 5) + '*'.repeat(digits.length - 5);
            formatDiv.textContent = "Phone Format Example: " + masked;
        } else {
            if (formatDiv) formatDiv.textContent = "";
        }
    }



    // document.querySelector("#newAddressForm").addEventListener("submit", function (e) {
    //     const fullNumber = iti.getNumber();
    //     phoneInput.value = fullNumber;
    // });



    function toggleConfirmButton() {
        const isSelected = $('input[name="address_id"]:checked').length > 0;
        $('#confirmOrderBtn').prop('disabled', !isSelected);
    }


    function saveSelectedAddress(input) {
        const addressId = $(input).val();
        sessionStorage.setItem('selectedAddressId', addressId);
    }


    function toggleEditLinks() {
        document.querySelectorAll('.form-check-input[name="address_id"]').forEach(radio => {
            const editLink = radio.closest('.form-check').querySelector('.edit-link');
            if (radio.checked) {
                editLink.style.display = 'inline';
            } else {
                editLink.style.display = 'none';
            }
        });
    }


    function storeEditInfo(event) {
        event.preventDefault();

        const link = $(event.currentTarget);
        const addressId = event.currentTarget.getAttribute('data-id');
        const productId = event.currentTarget.getAttribute('data-product-id');

        if (addressId) {
            sessionStorage.setItem('edit_address_id', addressId);
        }

        if (productId) {
            sessionStorage.setItem('edit_product_id', productId);
        }

        // Redirect to the address section
        window.location.href = link.attr('href');
    }
    function renderAddressLabel(radio) {
        const $radio = $(radio);
        const id = $radio.data('id');
        const label = $('#address-label-' + id);

        const name = $radio.data('name') || '';
        const phone = $radio.data('phone') || '';
        const email = $radio.data('email') || '';
        const building = $radio.data('building') || '';
        const street = $radio.data('street') || '';
        const landmark = $radio.data('landmark') || '';
        const city = $radio.data('city') || '';
        const pincode = $radio.data('pincode') || '';
        const state = $radio.data('state') || '';

        const formatted = `
        ${name} <br/> ${phone}<br>${email}<br>
        ${building}, ${street}, ${landmark}<br>
        ${city} - ${pincode}<br>
        ${state}
    `;
        toggleConfirmButton();

        label.html(formatted);
    }

    function generateAddressHtml(address) {
        const id = address.add_Id;
        const orderId = "<?= esc($od_Id) ?>";
        const selectedId = sessionStorage.getItem('selectedAddressId');
        const isChecked = selectedId == id ? 'checked' : '';

        return `
    <div class="form-check mb-2 position-relative" id="address-${id}">
        <input class="form-check-input" type="radio" name="address_id"
            value="${id}" ${isChecked}
            data-id="${id}"
            data-name="${address.add_Name}"
            data-phone="${address.add_Phone}"
            data-email="${address.add_Email}"
            data-building="${address.add_BuldingNo}"
            data-street="${address.add_Street}"
            data-landmark="${address.add_Landmark}"
            data-city="${address.add_City}"
            data-pincode="${address.add_Pincode}"
            data-state="${address.add_State}"
            onchange="renderAddressLabel(this); toggleEditLinks(); saveSelectedAddress(this);">
            <div class="row">
                <div class="col-8">
                    <label class="form-check-label" style="white-space: normal; word-break: break-word;" id="address-label-${id}">
                        ${address.add_Name} <br/> ${address.add_Phone}<br>${address.add_Email}<br>
                        ${address.add_BuldingNo}, ${address.add_Street}, ${address.add_Landmark}<br>
                        ${address.add_City} - ${address.add_Pincode}<br>
                        ${address.add_State}
                    </label>
                </div>
                <div class="col-4 text-end">
                    <a href="#" class="edit-link btn btn-sm btn-link"
                                            data-id="${a.add_Id}" data-product-id="${orderId}"
                                            onclick="openEditModal(this); return false;" style="display:none;">

                </div>
            </div>
        </a>
    </div>`;
    }
    $('#addNewTab').click(function () {
        $('#confirmOrderBtn').prop('disabled', true);
    });
    $('#selectExistAddr').click(function () {
        const isSelected = $('input[name="address_id"]:checked').length > 0;
        $('#confirmOrderBtn').prop('disabled', !isSelected);
    });
    function openEditModal(link) {
        const $link = $(link);

        $('#add_Id').val($link.data('id'));
        $('#add_CustId').val($link.data('custid'));
        $('#display_add_Id').val($link.data('id'));
        $('#pr_Id').val($link.data('product-id'));

        $('#add_Name').val($link.data('name'));
        $('#add_Email').val($link.data('email'));
        $('#add_Phone').val($link.data('phone'));
        $('#add_BuldingNo').val($link.data('building'));
        $('#add_Street').val($link.data('street'));
        $('#add_Landmark').val($link.data('landmark'));
        $('#add_City').val($link.data('city'));
        $('#add_State').val($link.data('state'));
        $('#add_Pincode').val($link.data('pincode'));

        // Optionally update intlTelInput if used on #add_Phone
        if (window.intlTelInput && window.phoneInputs && window.phoneInputs['#add_Phone']) {
            const iti = window.phoneInputs['#add_Phone'];
            iti.setNumber($link.data('phone'));
            $('#add_phcode').val("+" + iti.getSelectedCountryData().dialCode);
        }

        const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
        modal.show();
    }
    $(document).ready(function () {
        const selectedAddressId = sessionStorage.getItem('selectedAddressId');
        if (selectedAddressId) {
            const $radio = $('input[name="address_id"][value="' + selectedAddressId + '"]');
            if ($radio.length) {
                $radio.prop('checked', true);
                renderAddressLabel($radio[0]);
            }
        }
    });

    $(function () {

        // Save new address and then confirm order
        $('#newAddressForm').on('submit', function (e) {
            e.preventDefault();

            const $submitBtn = $('#newAddressForm button[type="submit"]');
            
            const phoneSelector = "#newPhone"; // Case-sensitive
            const phoneInput = $(phoneSelector)[0];
            const iti = window.phoneInputs[phoneSelector];

            if (!iti || !iti.isValidNumber()) {
                $('#phone_error').text("Invalid phone number").show();
                $('#phone_valid').hide();
                return;
            }

            const fullPhone = iti.getNumber();
            $('#newPhone').val(fullPhone); 
 const $submitBtnVal = $('#saveBtn');
    $submitBtnVal.prop('disabled', true);
            $.ajax({
                url: "<?= base_url('OrderNow/saveNewAddress') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json", // ensure JSON response is expected
                success: function (res) {
                    if (res.success === 1) {
                    $submitBtn.prop('disabled', true).show();

                        $('#no-address-message').hide();
                        toggleEditLinks();
                        const a = res.details;

                        const newHtml = `
                            <div class="form-check mb-2 position-relative" id="address-${a.add_Id}">
                                <input class="form-check-input" type="radio" name="address_id"
                                    value="${a.add_Id}" checked
                                    data-id="${a.add_Id}"
                                    data-name="${a.add_Name}"
                                    data-phone="${a.add_Phone}"
                                    data-email="${a.add_Email}"
                                    data-building="${a.add_BuldingNo}"
                                    data-street="${a.add_Street}"
                                    data-landmark="${a.add_Landmark}"
                                    data-city="${a.add_City}"
                                    data-pincode="${a.add_Pincode}"
                                    data-state="${a.add_State}"
                                    onchange="renderAddressLabel(this); toggleEditLinks(); saveSelectedAddress(this);">
                                    <div class="row">
                                        <div class="col-8">
                                            <label class="form-check-label" style="white-space: normal; word-break: break-word;" id="address-label-${a.add_Id}">
                                                ${a.add_Name} <br> ${a.add_Phone}<br>${a.add_Email}<br>
                                                ${a.add_BuldingNo}, ${a.add_Street}, ${a.add_Landmark}<br>
                                                ${a.add_City} - ${a.add_Pincode}<br>
                                                ${a.add_State}
                                            </label>
                                        </div>
                                        <div class="col-4 text-end">
                                            <a href="#" class="edit-link btn btn-sm btn-link"
                                                data-id="${a.add_Id}"
                                                data-custid="${a.add_CustId}"
                                                data-product-id="${orderId}"
                                                data-name="${a.add_Name}"
                                                data-phone="${a.add_Phone}"
                                                data-email="${a.add_Email}"
                                                data-building="${a.add_BuldingNo}"
                                                data-street="${a.add_Street}"
                                                data-landmark="${a.add_Landmark}"
                                                data-city="${a.add_City}"
                                                data-pincode="${a.add_Pincode}"
                                                data-state="${a.add_State}"
                                                onclick="openEditModal(this); return false;" style="display:none;"><span class="edit-address-orders">EDIT</span>
                                            </a>
                                        </div>
                                    </div>
                            </div>`;

                        $('#selectExistAddress').append(newHtml);
                        const $newRadio = $(`input[name="address_id"][value="${a.add_Id}"]`);
                        if ($newRadio.length) {
                            $newRadio.prop('checked', true); // mark it checked
                            renderAddressLabel($newRadio[0]); // update label content
                            toggleEditLinks(); // show the edit link for the selected radio
                            toggleConfirmButton(); // enable the confirm button
                        }

                        $('#messageBox').html('<div class="alert alert-success">' + res.message + '</div>').fadeIn().delay(5000).fadeOut();
                        // Optionally reset form
                        $('#newAddressForm')[0].reset();
                        $('html, body').animate({
                            scrollTop: $('#address-' + a.add_Id).offset().top - 100
                        }, 'slow');

                        $('#collapseNew').collapse('hide');
                        $('#collapseSelect').collapse('show');

                        sessionStorage.setItem('selectedAddressId', a.add_Id);
                        
                        setTimeout(() => {
                         window.location.reload();
                        },150);
                        
                    } else {
                        $('#messageBox').html('<div class="alert alert-danger">' + res.message + '</div>').fadeIn().delay(5000).fadeOut();
                        $submitBtnVal.prop('disabled', false);
                    }
                },
                error: function () {
                    $('#messageBox').html('<div class="alert alert-danger">Failed to save address.</div>').fadeIn().delay(5000).fadeOut();
                    $('html, body').animate({
                        scrollTop: $('#messageBox').offset().top - 100
                    }, 'slow');
                     $submitBtnVal.prop('disabled', false);
                },
                complete: function () {
                    setTimeout(() => {
                        // $submitBtn.prop('disabled', true).show();
                         $submitBtnVal.prop('disabled', false);
                    }, 5000);
                }
            });
        });
        });
        $(document).on('change', 'input[name="address_id"]', function () {
            const selectedId = $(this).val();
            sessionStorage.setItem('selectedAddressId', selectedId);
            toggleEditLinks();
            toggleConfirmButton();
        });


        document.addEventListener('DOMContentLoaded', toggleEditLinks);

        $(document).ready(function () {
            const selectedAddressId = sessionStorage.getItem('selectedAddressId');
            if (selectedAddressId) {
                const $radio = $('input[name="address_id"][value="' + selectedAddressId + '"]');
                if ($radio.length) {
                    $radio.prop('checked', true);
                }
            }
            // Render labels
            $('input[name="address_id"]').each(function () {
                renderAddressLabel(this);
            });
            toggleConfirmButton();
            toggleEditLinks();

            const editAddressId = sessionStorage.getItem('edit_address_id');
            const editProductId = sessionStorage.getItem('edit_product_id');

            if (editAddressId) {
                // Switch to address tab
                const addressTabTrigger = document.querySelector('#address-tab');
                if (addressTabTrigger) {
                    const tab = new bootstrap.Tab(addressTabTrigger);
                    tab.show();
                }

                // Load address data via existing function
                setTimeout(() => {
                    if (typeof editAddress === 'function') {
                        editAddress(editAddressId);
                    }

                    if (editProductId) {
                        $('#edit_product_id').val(editProductId); // example
                    }
                    const $editedRadio = $('input[name="address_id"][value="' + editAddressId + '"]');
                    if ($editedRadio.length) {
                        $editedRadio.prop('checked', true);
                        sessionStorage.setItem('selectedAddressId', editAddressId);

                        renderAddressLabel($editedRadio[0]);
                    }

                    toggleEditLinks();
                    const $addressBlock = $('#address-' + editAddressId);
                    if ($addressBlock.length) {
                        $('html, body').animate({
                            scrollTop: $addressBlock.offset().top - 100
                        }, 600);
                    }
                }, 300);

                // Clean up
                sessionStorage.removeItem('edit_address_id');
                sessionStorage.removeItem('edit_product_id');
            }
        });
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


        $('#update_address').on('click', function (e) {
            e.preventDefault();
            const $form = $('#editAddressForm');
            const $alertBox = $('#EditAddressModalAlert');

            if (!isPhoneValid("#add_Phone")) {
                $alertBox
                    .removeClass('alert-success d-none')
                    .addClass('alert-danger')
                    .html('Please enter a valid phone number.')
                    .fadeIn()
                    .delay(4000)
                    .fadeOut();
                $('html, body').animate({
                    scrollTop: 0
                }, 'smooth');
                return;
            }

            appendPhoneData("#update_address", "#add_Phone", "add_phcode");

            $.post("<?= base_url('profile/address/edit') ?>", $form.serialize(), function (res) {
                if (res.status === '1') {
                    // alert("keri");
                    $alertBox
                        .removeClass('alert-danger d-none')
                        .addClass('alert-success')
                        .html('Address Updated Successfully!')
                        .fadeIn()
                        .delay(2000)
                        .fadeOut();
                        $('#editAddressModal').modal('hide');

                    setTimeout(() => {
                        window.location.reload();
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
        
        $('#confirmOrderBtn').on('click', function () {
            const $btn = $(this);
            const od_Id = $btn.data('odid');
            // const add_Id = $('input[name="address_id"]:checked').val();
            const add_Id = sessionStorage.getItem('selectedAddressId') || $('input[name="address_id"]:checked').val();

            // alert(add_Id);
            if (!add_Id) {
                $('#messageBox')
                    .html('<div class="alert alert-warning">Please select or add an address.</div>')
                    .fadeIn()
                    .delay(5000)
                    .fadeOut();
                $('html, body').animate({
                    scrollTop: $('#messageBox').offset().top - 100
                }, 'slow');
                return;
            }

            $btn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: "<?= base_url('OrderNow/submitfrm') ?>",
                type: "POST",
                data: { od_Id, add_Id },
                dataType: "json",
                success: function (res) {
                    if (res.status === 1) {
                        const newAddress = res.newAddress;
                        if (newAddress && newAddress.add_Id) {
                            setTimeout(() => {
                                $(`.form-check input[value="${newAddress.add_Id}"]`).closest('.form-check').remove();

                                const addressHtml = generateAddressHtml(newAddress); // Build new address radio + label block
                                $('#selectExistAddress').append(addressHtml); // Append to container

                                // Re-render label content and set new radio as checked
                                const newRadio = $(`input[name="address_id"][value="${newAddress.add_Id}"]`)[0];
                                if (newRadio) {
                                    newRadio.checked = true;
                                    renderAddressLabel(newRadio);
                                    toggleEditLinks();
                                }

                            },1500);     
                        }

                        $('#messageBox')
                            .html('<div class="alert alert-success">' + res.msg + '</div>')
                            .fadeIn()
                            .delay(5000)
                            .fadeOut();
                        setTimeout(function () {
                            sessionStorage.removeItem('selectedSize');
                            sessionStorage.removeItem('selectedColor');
                            sessionStorage.removeItem('selectedQty');
                            sessionStorage.removeItem('tempOrder');
                            window.location.href = res.redirect;

                        }, 3000);

                    } else {
                        $('#messageBox')
                            .html('<div class="alert alert-danger">' + res.msg + '</div>')
                            .fadeIn()
                            .delay(5000)
                            .fadeOut();

                    }

                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: $('#messageBox').offset().top - 100
                    }, 'slow');

                    // $btn.prop('disabled', false).text('Confirm Order');
                },
                error: function () {
                    $('#messageBox')
                        .html('<div class="alert alert-danger">Failed to submit order.</div>')
                        .fadeIn()
                        .delay(5000)
                        .fadeOut();

                    $('html, body').animate({
                        scrollTop: $('#messageBox').offset().top - 100
                    }, 'slow');

                    // $btn.prop('disabled', false).text('Confirm Order');
                }
            });
        });

    


</script>