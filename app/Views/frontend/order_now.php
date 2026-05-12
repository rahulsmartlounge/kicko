<?php $hasDefaultAddress = !empty($details['address']);
?>

<section class="hero-banner">
    <div class="container-lg">
        <h4>PLACE YOUR ORDER</h4>
        <div class="row order-box">
            <div>&nbsp;</div>

            <!-- Left Panel: Order Form -->
            <div class="col-md-7">
                <div class="mb-3">
                    <h6>Submit To Confirm And Place Your Order.</h6>
                </div>

                <div id="messageBox" class="alert" style="display: none;"></div>

                <div class="accordion mb-4" id="addressAccordion">
                    <!-- Existing Addresses -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSelect">
                            <button class="accordion-button" id="selectExistAddr" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseSelect">
                                Select Existing Address
                            </button>
                        </h2>
                        <div id="collapseSelect" class="accordion-collapse collapse show"
                            data-bs-parent="#addressAccordion">
                            <div class="accordion-body" id="selectExistAddress">
                                <?php if (!empty($addresses)): ?>
                                    <?php foreach ($addresses as $address): ?>
                                        <div class="form-check mb-2 position-relative">
 
                                            <input type="hidden" name="edit_address_id" id="edit_address_id" value="">
                                            <input type="hidden" name="edit_product_id" id="edit_product_id" value="">
                                            <input class="form-check-input" type="radio" name="address_id"
                                                value="<?= $address['add_Id'] ?>" <?= $address['add_Default'] ? 'checked' : '' ?>
                                                data-id="<?= $address['add_Id'] ?>" data-name="<?= esc($address['add_Name']) ?>"
                                                data-phone="<?= esc($address['add_Phone']) ?>"
                                                data-email="<?= esc($address['add_Email']) ?>"
                                                data-building="<?= esc($address['add_BuldingNo']) ?>"
                                                data-street="<?= esc($address['add_Street']) ?>"
                                                data-landmark="<?= esc($address['add_Landmark']) ?>"
                                                data-city="<?= esc($address['add_City']) ?>"
                                                data-pincode="<?= esc($address['add_Pincode']) ?>"
                                                data-state="<?= esc($address['add_State']) ?>"
                                                onchange="renderAddressLabel(this); toggleEditLinks();  saveSelectedAddress(this);">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <label class="form-check-label "  style="white-space: normal; word-break: break-word;"  id="address-label-<?= $address['add_Id'] ?>">
                                                            <!-- You can add some visible address preview if needed here -->
                                                            <?= esc($address['add_Name']) ?><br/>, <?= esc($address['add_City']) ?> -
                                                            <?= esc($address['add_Pincode']) ?><br/><?= esc($address['add_Email']) ?>                                                
                                                        </label>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                         <a href="#" class="edit-link btn btn-sm btn-link"
                                                        data-id="<?= $address['add_Id'] ?>"
                                                        data-name="<?= esc($address['add_Name']) ?>"
                                                        data-phone="<?= esc($address['add_Phone']) ?>"
                                                        data-email="<?= esc($address['add_Email']) ?>"
                                                        data-building="<?= esc($address['add_BuldingNo']) ?>"
                                                        data-street="<?= esc($address['add_Street']) ?>"
                                                        data-landmark="<?= esc($address['add_Landmark']) ?>"
                                                        data-city="<?= esc($address['add_City']) ?>"
                                                        data-pincode="<?= esc($address['add_Pincode']) ?>"
                                                        data-state="<?= esc($address['add_State']) ?>"
                                                        data-custid="<?= esc($address['add_CustId']) ?>"
                                                        data-product-id="<?= esc($od_Id) ?>"
                                                        onclick="openEditModal(this); return false;" style="display:none;">
                                                        <span class="edit-address-orders">EDIT</span></a>
                                                    </div>
                                                </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p id="no-address-message">No saved addresses. Please add a new address.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                  
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingNew">
                            <button class="accordion-button collapsed" id="addNewTab" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNew">
                                Add New Address
                            </button>
                        </h2>
                        <div id="collapseNew" class="accordion-collapse collapse" data-bs-parent="#addressAccordion">
                            <div class="accordion-body">
                                <form id="newAddressForm">
                                    <input type="hidden" name="od_Id" value="<?= esc($od_Id) ?>">

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="newName" class="form-control" placeholder="Full Name" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="email" name="newEmail" class="form-control" placeholder="Email" required>
                                        </div>     
                                        <div class="col-md-6 mb-2 phn_code">
                                            <input name="newPhone" id="newPhone" type="tel" class="form-control" placeholder="" required>
                                        </div>
                                        <div id="phone_error" class="text-danger small" style="display:none;"></div>
                                        <div id="phone_valid" class="text-success small" style="display:none;">Valid Number</div>
                                        <input type="hidden" name="newphcode" id="newphcode">
                                        <div id="phone_format" class="text-muted small mt-1"></div>
                                    
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="newBuilding" class="form-control" placeholder="Building No" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="newStreet" class="form-control" placeholder="Street" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="newLandmark" class="form-control" placeholder="Landmark">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="newCity" class="form-control" placeholder="City" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="newState" class="form-control" placeholder="State" required pattern="[A-Za-z\s]+" title="No Numeric Or Special Characters Allowed">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="newPincode" class="form-control" placeholder="Pincode" maxlength="10" minlenght="4" required>
                                        </div>
                                    </div>

                                    <button type="submit" id="saveBtn" name="saveBtn" class="btn btn-primary mt-2">Save & Use This Address</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div>&nbsp;</div>
                    <button class="btn btn-success" id="confirmOrderBtn" data-odid="<?= esc($od_Id) ?>">Confirm
                        Order</button>

                </div>

                <!-- Confirm Button -->
            </div>

            <!-- Right Panel: Product Summary -->
            <div class="col-md-5">
                <div class="mb-3">
                    <h6>Order Details</h6>
                </div>
                <div class="row">
                    <?php
                    $decoded = json_decode($product->product_images ?? '', true);
                    $firstImage = is_array($decoded) && isset($decoded[0]['name'][0])
                        ? base_url('uploads/productmedia/' . $decoded[0]['name'][0])
                        : base_url('uploads/productmedia/default.jpg');
                    ?>
                    <div class="col-md-5">
                        <img src="<?= $firstImage ?>" style="width: 100px;" alt="Product Image" />
                    </div>
                    <div class="col-md-7">
                        <div><b><?= esc($product->pr_Name ?? '') ?></b></div>
                        <p>Product Code: <?= esc($product->pr_Code ?? '') ?></p>
                        <p>Price: ₹<?= esc($order->od_Selling_Price ?? '') ?></p>
                        <p>Quantity: <?= esc($order->od_Quantity ?? '') ?></p>
                        <p>Grand Total: ₹<?= esc($order->od_Grand_Total ?? '') ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <h6>Important Note!</h6>
                    <p>Once you submit the order form, our executive will contact you via phone or WhatsApp. Your order
                        will be dispatched after confirmation via call.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="editAddressForm" class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="editAddressModalLabel"><b>Update Address</b></h5>
                    <small class="text-muted mt-2">Make sure all required fields are filled correctly before
                        updating.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body  p-3">
                <div class="alert alert-success d-none" role="alert" id="EditAddressModalAlert"></div>

                <div id="editAlert"></div>
                <input type="hidden" name="add_Id" id="add_Id" />
                <input type="hidden" name="add_CustId" id="add_CustId" />
                <input type="hidden" name="display_add_Id" id="display_add_Id" />
                <input type="hidden" name="pr_Id" id="pr_Id" />

                <div class="row">
                    <div class="col-md-6 col-12">
                        <span style="color: red;">*</span>
                        <lable><b>Name</b></label>
                            <input type="text" name="add_Name" id="add_Name" class="form-control"
                                placeholder="Full Name" required>
                            <div>&nbsp;</div>

                    </div>
                    <div class="col-md-6 col-12">
                        <span style="color: red;">*</span>
                        <label><b>Email</b></label>
                        <input type="email" name="add_Email" id="add_Email" class="form-control" placeholder="Email"
                            required>
                        <div>&nbsp;</div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 col-12">


                        <div class="phn_code"> <span style="color: red;">*</span>
                            <label><b>Phone Number</b></label>
                            <input type="tel" maxlength="15" minlength="7" name="add_Phone" id="add_Phone"
                                class="form-control" placeholder="" required>

                            <div id="add_Phone_error" class="text-danger small" style="display:none; font-size:17px;">
                            </div>
                            <div id="add_Phone_valid" class="text-success small" style="display:none;">Valid Number
                            </div>
                            <div id="add_Phone_format" class="text-muted small mt-1"></div>
                            <input type="hidden" name="add_phcode" id="add_phcode">
                            <div>&nbsp;</div>
                        </div>

                    </div>
                    <div class="col-md-6 col-12">
                        <span style="color: red;">*</span>
                        <label><b>Building Number</b></label>
                        <input type="text" name="add_BuldingNo" id="add_BuldingNo" class="form-control"
                            placeholder="Building No" required>
                        <div>&nbsp;</div>
                    </div>
                </div>

                <div class="row ">
                    <div class="col-md-6 col-12">
                        <span style="color: red;">*</span>
                        <label><b>Street</b></label>
                        <input type="text" name="add_Street" id="add_Street" class="form-control" placeholder="Street"
                            required>
                        <div>&nbsp;</div>
                    </div>
                    <div class="col-md-6 col-12">
                        <label><b>Landmark</b></label>
                        <input type="text" name="add_Landmark" id="add_Landmark" class="form-control"
                            placeholder="Landmark">
                        <div>&nbsp;</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <span style="color: red;">*</span>
                        <label><b>City</b></label>
                        <input type="text" name="add_City" id="add_City" class="form-control" placeholder="City"
                            required>
                        <div>&nbsp;</div>
                    </div>
                    <div class="col-md-6 col-12">
                        <span style="color: red;">*</span>
                        <label><b>State</b></label>
                        <input type="text" name="add_State" id="add_State" class="form-control" placeholder="State"
                            required>
                        <div>&nbsp;</div>

                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 col-12">
                        <span style="color: red;">*</span>
                        <label><b>Pincode</b></label>
                        <input name="add_Pincode" id="add_Pincode" class="form-control" placeholder="Pincode"
                            maxlength="10" minlenght="4" required>
                        <div>&nbsp;</div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div>

                    </div>
                    <button type="button" name="update_address" id="update_address" class="btn btn-info mt-2">Update
                        Address</button>
                </div>

            </div>
        </form>
    </div>
</div>