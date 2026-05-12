<div class="container mt-4 profile-main-tab">
    <ul class="nav nav-tabs nav-profile" id="profileTabs" role="tablist">
        <li class="nav-item">
           <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Profile</button>

        </li>
      <li class="nav-item">
    <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab">Address</button>
</li>
<li class="nav-item">
    <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">Orders</button>
</li>
<li class="nav-item">
    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
        <span class="d-none d-sm-inline">Update</span> Password
    </button>
</li>

    </ul>

    <div class="tab-content" id="profileTabContent">
          <div> &nbsp; </div>
        <div id="messageBox" class="alert" style="display: none;"></div>
        <div> &nbsp; </div>
        <!-- Profile Tab -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <form id="profileForm" method="post">
                        <?php if (!empty($user)): ?>
                            <div>&nbsp;</div>
                            <input type="text" name="profilename" id="profilename" class="form-control"
                                value="<?= esc($user['cust_Name']) ?>" pattern="[A-Za-z\s]+"
                                title="Only letters are allowed"
                                oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" />
                            <div>&nbsp;</div>
                            <input type="email" name="email" id="email" class="form-control"
                                value="<?= esc($user['cust_Email']) ?>" readonly />
                            <div>&nbsp;</div>
                            <div class="phn_code">
                                <input id="phone" name="phone" type="tel" class="form-control" placeholder=""
                                    value="<?= esc($user['cust_Phone']) ?>" >
                            </div>

                            <div id="phone_error" class="text-danger small" style="display:none;"></div>
                            <div id="phone_valid" class="text-success small" style="display:none;">Valid Number</div>
                            <input type="hidden" name="cust_phcode" id="cust_phcode">
                            <div id="phone_format" class="text-muted small mt-1"></div>
                            <div>&nbsp;</div>
                        <?php else: ?>
                            <div class="alert alert-danger">User information not found.</div>
                        <?php endif; ?>
                        <div class="text-end">
                            <button class="btn btn-primary mt-2" type="submit">Update</button>
                        </div>
                        <div>&nbsp;</div>
                    </form>

                    <div>&nbsp;</div>
                </div>
            </div>
        </div>

        <!-- Address Tab -->
        <div class="tab-pane fade" id="address" role="tabpanel">
            



            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success" id="flashMessage">
                    <?= session()->getFlashdata('message') ?>
                </div>
            <?php endif; ?>
   <div class="col-md-12">
                        <button class="btn btn-success mb-2" onclick="openAddAddressForm()">+ Add Address</button>

                        <!-- <a href="#" style="text-align:right;">Continue...</a> -->
                    </div>
                    <div>&nbsp;</div>
                       <div id="addressFormContainer" style="display:none">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="addressForm">
                                <input type="hidden" name="id" id="addressId" />
                                <div class="mb-2">
                                    <input type="text" class="form-control" id="newName" name="newName"
                                        placeholder="Full Name" required>
                                </div>
                                <div class="mb-2"><input type="email" class="form-control" id="newEmail" name="newEmail"
                                        placeholder="Email" required></div>
                                <div class="mb-2 phn_code">



                                    <input type="tel" class="form-control" id="newPhone" name="newPhone" placeholder=""
                                        required>
                                    <div id="newPhone_error" class="text-danger small" style="display:none;"></div>
                                    <div id="newPhone_valid" class="text-success small" style="display:none;">Valid
                                        Number</div>
                                    <div id="newPhone_format" class="text-muted small mt-1"></div>
                                    <input type="hidden" name="new_phcode" id="new_phcode">
                                </div>

                                <div class="mb-2"><input type="text" class="form-control" id="newBuilding"
                                        name="newBuilding" placeholder="Building No." required></div>
                                <div class="mb-2"><input type="text" class="form-control" id="newStreet"
                                        name="newStreet" placeholder="Street" required></div>
                                <div class="mb-2"><input type="text" class="form-control" id="newLandmark"
                                        name="newLandmark" placeholder="Landmark"></div>
                                <div class="mb-2"><input type="text" class="form-control" id="newCity" name="newCity"
                                        placeholder="City" required></div>
                                <div class="mb-2"><input type="text" class="form-control" id="newState" name="newState"
                                        placeholder="State" required></div>
                                <div class="mb-2">
        <input type="text"  class="form-control" id="newPincode" name="newPincode" placeholder="Pincode" maxlength="10" pattern="\d{1,10}" inputmode="numeric" required>

</div>
                                <!-- <label><input type="checkbox" name="is_default" id="is_default" /> Set as default address</label> -->
                                <div class="text-end">
                                    <button class="btn btn-secondary mt-2 me-2" type="button"
                                        onclick="discardAddressForm()">Discard</button>
                                 
                                    <button type="submit" id="saveAddressBtn" class="btn btn-success mt-2">Save Address</button>

                                </div>
                            </form>
                            <div>&nbsp;</div>
                        </div>
                    </div>
                </div>
            <div id="addressList">
                <div class="row">
                    <?php if (!empty($addresses)): ?>
                        <div class="row">
                            <?php foreach ($addresses as $addr): ?>
                                <div class="col-md-6 mb-3" id="address_card_<?= $addr['add_Id'] ?>">
                                    <div class="card p-3 h-100">
                                        <strong><?= esc($addr['add_Name']) ?></strong><br>
                                        <?= esc($addr['add_BuldingNo']) ?>, <?= esc($addr['add_Street']) ?><br>
                                        <?php if (!empty($addr['add_Landmark'])): ?>
                                            <?= esc($addr['add_Landmark']) ?><br>
                                        <?php endif; ?>
                                        <?= esc($addr['add_City']) ?>, <?= esc($addr['add_State']) ?> -
                                        <?= esc($addr['add_Pincode']) ?><br>
                                        Phone: <?= esc($addr['add_Phone']) ?> | Email: <?= esc($addr['add_Email']) ?><br>
                                        <div class="mt-2">
                                            <a href="javascript:void(0)" onclick="editAddress(<?= $addr['add_Id'] ?>)">Edit</a>
                                            |
                                            <a href="#" onclick="openDeleteModal(<?= $addr['add_Id'] ?>)">Remove</a>
                                            <?php if (!empty($addr['add_Default']) && $addr['add_Default'] == 1): ?>
                                                | <span>Default</span>
                                            <?php else: ?>
                                                | <a href="javascript:void(0);"
                                                    onclick="setDefaultAddress(<?= $addr['add_Id'] ?>)">Set as Default</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                 

                </div>
                
            </div>
        </div>

        <!-- Orders Tab -->
        <div class="tab-pane fade" id="orders" role="tabpanel">
            <div>&nbsp;</div>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $decoded = json_decode($order['product_images'], true);
                    $firstImage = is_array($decoded) && isset($decoded[0]['name'][0])
                        ? base_url('uploads/productmedia/' . $decoded[0]['name'][0])
                        : base_url('assets/img/no-image.png');
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card p-3 shadow-sm h-100">
                            <div class="row g-3 ">
                                <div class="col-md-4">
                                    <a href="<?= base_url('product/product_details/' . $order['pr_Id']); ?>">
                                        <img src="<?= esc($firstImage) ?>" class="img-fluid rounded order-image"
                                            style="max-width: 100%;" alt="Product Image" />
                                    </a>
                                </div>
                                <div class="col-md-8">
                                    <a href="<?= base_url('product/product_details/' . $order['pr_Id']); ?>"
                                        class="text-decoration-none text-dark">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong><?= esc($order['pr_Name']) ?></strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <b>Ordered On:</b><br />
                                                <?= date('d M Y', strtotime($order['od_createdon'])) ?> &nbsp;<br />

                                            </div>
                                        </div>
                                    </a>
                                    Product Code: <?= esc($order['pr_Code']) ?><br>
                                    Size: <?= esc($order['od_Size']) ?><br>
                                    Quantity: <?= esc($order['od_Quantity']) ?><br>
                                    <?php
                                    $statusMap = [
                                        1 => 'New',
                                        2 => 'Confirmed',
                                        3 => 'Packed',
                                        4 => 'Dispatched'
                                    ];
                                    ?>
                                    <b>Order Status:</b> <?= esc($statusMap[$order['od_Status']] ?? 'Unknown') ?><br>
                                    <?php if ($order['od_Status'] == 4): ?>
                                        <div class="col-md-12">
                                            <a href="<?= esc($order['tracker_Link']) ?>" target="_blank"><button
                                                    class="btn btn-warning">Track Your Order</button></a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-12"><a
                                            href="<?= base_url('review/' . $order['cus_Id'] . '/' . $order['pr_Id']) ?>"
                                            class="btn btn-link p-0" style="text-decoration:none;">Write a Review</a>
                                    </div>
                                    <div class="col-md-12" style="padding-top:8px;">
                                        <a href="<?= base_url('product/product_details/' . $order['pr_Id']); ?>">
                                            <button class="btn buyAgain"><i class="bi bi-bag-fill"></i> &nbsp;
                                                Buy it again
                                            </button>
                                        </a>
                                    </div>

                                </div>
                                <div>
                                    <hr />
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Shipping Address</strong>
                                        <?php
                                        $address = $order['od_Shipping_Address'];

                                        $addressWithoutEmail = preg_replace('/[\s,]*[^@\s,]+@[^@\s,]+\.[^@\s,]+/', '', $address);

                                        preg_match('/\+?\d[\d\s\-]{7,}/', $addressWithoutEmail, $matches);
                                        $phone = isset($matches[0]) ? trim($matches[0]) : '';

                                        $addressClean = trim(str_replace($phone, '', $addressWithoutEmail), ", ");

                                        $parts = explode(',', $addressClean, 2);
                                        ?>

                                        <div>
                                            <?= esc(trim($parts[0])) ?><br>
                                            <?= isset($parts[1]) ? esc(trim($parts[1])) . '<br>' : '' ?>
                                            <?= !empty($phone) ? esc($phone) : '' ?>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <strong>Order Summary</strong>
                                        <div>Order Id:<?= esc($order['od_Id']) ?> </div>
                                        <div>Price:<?= esc(round($order['od_Original_Price'])) ?> </div>
                                        <?php if (!empty($order['od_DiscountValue'])): ?>
                                            <div>
                                                Discount:<?= esc(round($order['od_DiscountValue'])) ?><?= esc($order['od_DiscountType']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div><strong>Grand Total:</strong><?= esc(round($order['od_Grand_Total'])) ?> </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        <!-- Change Password Tab -->
        <div class="tab-pane fade" id="password" role="tabpanel">
            <div>&nbsp;</div>
            <div class="row">
                <div class="col-md-6">
                    <form id="changePasswordForm" method="post">
                        <div class="mb-2 position-relative">
                            <input type="password" name="oldPassword" id="oldPassword" class="form-control"
                                maxlength="15" placeholder="Old Password">
                            <i class="toggle-password fa fa-eye-slash position-absolute"
                                style="top: 12px; right: 10px; cursor: pointer;" data-target="oldPassword"></i>
                        </div>
                        <div class="mb-2 position-relative">
                            <input type="password" name="newPassword" id="newPassword" class="form-control"
                                maxlength="15" placeholder="New Password">
                            <i class="toggle-password fa fa-eye-slash position-absolute"
                                style="top: 12px; right: 10px; cursor: pointer;" data-target="newPassword"></i>
                        </div>
                        <div class="progress mt-2" id="new-password-strength-bar" style="height: 8px; display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%;"
                                id="new-password-strength-fill">
                            </div>
                        </div>
                        <small id="new-password-strength-text" class="fw-bold"></small>

                        <div class="mb-2 position-relative">
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
                                maxlength="15" placeholder="Confirm Password">
                            <i class="toggle-password fa fa-eye-slash position-absolute"
                                style="top: 12px; right: 10px; cursor: pointer;" data-target="confirmPassword"></i>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary mt-2" type="submit">Update Password</button>
                        </div>
                        <div id="passwordResponse" class="mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Edit Address Modal -->
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
                    <button type="button"  name="update_address" id="update_address" class="btn btn-info mt-2">Update Address</button>
                </div>

            </div>
        </form>
    </div>
</div>
<!-- Bootstrap Modal -->

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog" style="max-width: 500px; margin: 10px auto;">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <h4 class="mb-3"><b>Are You Sure?</b></h4>
                <i class="fa fa-trash fa-5x animated-icon mb-3" style="color: gray;"></i>
                <p class="mb-4">Do you want to delete the address </p>
                <form method="post" action="<?= base_url('profile/deleteAddress') ?>">
                    <input type="hidden" id="delete_add_id" name="add_Id">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" onclick="confirmDeleteAddress()" class="btn btn-primary px-4">Yes</button>
                        <button type="button" class="btn px-4" style="background-color: black; color: white;"
                            data-bs-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>