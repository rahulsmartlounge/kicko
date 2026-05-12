    <script>
    // Order View JS
    $(document).ready(function () {
        var orderId = <?= json_encode($od_Id) ?>;
        let originalStatus = '';

        $.ajax({
            url: '<?= base_url('admin/orders/view/') ?>' + orderId,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    const order = res.data.order;
                    const customer = res.data.customer;
                    const address = res.data.address;

                    originalStatus = order.od_Status;

                    $('#customer-details').html(`
                    <p><strong>Name:</strong> ${customer.cust_Name}</p>
                    <p><strong>Email:</strong> ${customer.cust_Email}</p>
                    <p><strong>Phone:</strong> ${customer.cust_Phone ? customer.cust_Phone : 'N/A'}</p>
                `);

                    $('#order-details').html(`
                    <p><strong>Product Code:</strong> ${order.pr_Code}</p>
                    <p><strong>Product Name:</strong> ${order.pr_Name}</p>
                    <p><strong>Description:</strong> ${order.pr_Description}</p>
                    <p><strong>Quantity:</strong> ${order.od_Quantity}</p>
                   
                    <p><strong>Ordered On:</strong> ${new Date(order.od_createdon).toLocaleString()}</p>
                    <p><strong>Original Price Per Piece:</strong> ${Math.round(order.od_Original_Price)}</p>
                    <p><strong>Discount:</strong> ${order.od_DiscountValue}</p>
                    <p><strong>Selling Price Per Piece:</strong> ${Math.round(order.od_Selling_Price)}</p>
                    <p><strong>Grand Total:</strong> ${Math.round(order.od_Grand_Total)}</p>
                    <p><strong>Discount Type:</strong> ${order.od_DiscountType}</p><hr/>

                    <div class="alert p-2" id="alertBox" style="display:none; font-size: 14px;"></div>

                    <div class="form-group row align-items-center card-block">
                        <label class="col-auto col-form-label"><strong>Update Status:</strong></label>
                        <div class="col-auto">
                            <select class="form-control form-control-sm arrow" style="font-size: 12px;" id="orderStatus" name="orderStatus">
                                <option value="1" ${order.od_Status === '1' ? 'selected' : ''}>New</option>
                                <option value="2" ${order.od_Status === '2' ? 'selected' : ''}>Confirmed</option>
                                <option value="3" ${order.od_Status === '3' ? 'selected' : ''}>Packed</option>
                                <option value="4" ${order.od_Status === '4' ? 'selected' : ''}>Dispatched</option>
                            </select>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-sm btn-primary" id="orderUpdatedId">Update</button>
                        </div>
                    </div>

                    <div id="tracking-link" class="form-group card-block mt-2" style="display: none;">
                        <label for="trackingUrl"><strong>Tracking Link:</strong></label>
                        <textarea class="form-control form-control-sm" id="trackingUrl" name ="trackingUrl" rows="2" placeholder="Enter tracking link here...">${order.tracker_Link ? order.tracker_Link : ''}</textarea>
                    </div>
                `);

                    $('#orderStatus').on('change', function () {
                        if ($(this).val() === '4') {
                            $('#tracking-link').show();
                        } else {
                            $('#tracking-link').hide();
                        }
                    });

                    $('#orderStatus').trigger('change');
                    const Deliveraddress = order.od_Shipping_Address || '';
                    const parts = Deliveraddress.split(',').map(p => p.trim());

                    // Safety checks
                    const name = parts[0] || '';
                    const phone = parts.find(p => /\+?\d[\d\s\-()]{7,}/.test(p)) || '';
                    const email = parts.find(p => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(p)) || '';

                    // Get the middle part (excluding name, phone, email)
                    const middle = parts.filter(p =>
                        p !== name && p !== phone && p !== email
                    ).join(', ');

                    $('#delivery-details').html(`
                    <p><strong>Name:</strong> ${name}</p>
                    <p>${middle}</p>
                    <p><strong>Phone:</strong> ${phone}</p>
                    <p><strong>Email:</strong> ${email}</p>
                                    
                `);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                $('#customer-details').html('<p>Error loading data.</p>');
            }
        });

        $(document).on('click', '#orderUpdatedId', function () {
            const tracker = $('#trackingUrl').val();
            const currentStatus = $('#orderStatus').val();

            if (currentStatus === originalStatus) {
                $('#alertBox')
                    .removeClass()
                    .addClass('alert alert-warning p-2')
                    .text("No change in status to update.")
                    .fadeIn()
                    .delay(2000)
                    .fadeOut();
                return;
            }
            $.ajax({
                url: '<?= base_url('admin/orders/loadStatus/') ?>' + orderId,
                type: 'POST',
                dataType: 'json',
                data: {
                    tracker: tracker,
                    status: currentStatus
                },

                success: function (response) {
                    if (response.status === true) {
                        $('#alertBox')
                            .removeClass()
                            .addClass('alert alert-success p-2')
                            .text(response.message)
                            .fadeIn()
                            .delay(2000)
                            .fadeOut();

                        originalStatus = currentStatus; // Save the new valid status
                    } else {
                        // This will show message like: 'Enter the tracking link here.'
                        $('#alertBox')
                            .removeClass()
                            .addClass('alert alert-danger p-2')
                            .text(response.message)
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                    }
                },
                error: function (xhr, status, error) {
                    $('#alertBox')
                        .removeClass()
                        .addClass('alert alert-danger p-2')
                        .text('Failed to Update Status: ' + xhr.responseText)
                        .fadeIn()
                        .delay(3000)
                        .fadeOut();
                }

            });
        });
    });
    $(document).on('click', '#backToOrders', function () {
        window.location.href = "<?= base_url('admin/orders') ?>";
    });



</script>