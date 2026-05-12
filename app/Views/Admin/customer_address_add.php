<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10"><?= isset($cust) ? 'Update Customer Delivery Address' : 'Add Customer Delivery Address'; ?></h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('admin/dashboard') ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#"><?= isset($cust) ? 'Delivery Address' : 'Delivery Address'; ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page-header end -->
    <div class="pcoded-inner-content">
        <!-- Main-body start -->
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Page-body start -->
                <div class="page-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-2">

                                        </div>
                                        <div class="col-md-7">

                                        </div>
                                        <div class="col-md-2">
                                            <div class="row">
                                                <div class="col-lg-12 d-flex justify-content-end p-2">
                                                  
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block">
								<div id="messageBox" class="alert alert-success" style="display: none;"></div>
                                    <form name="createcustaddress" id="createcustaddress" method="post">
                                        <div class="form-group row">
                                           <label class="col-sm-2 col-form-label">Name <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="custname" id="custname" class="form-control" maxlength="30" autocomplete="off"
                                                    value="<?= isset($address) ? ($address['add_Name']) : '' ?>" placeholder="Enter the customer name" * required>
											<span class="text-danger error-msg" id="error-custname"></span>
											</div>
											
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Contact Number <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="mobile" id="mobile" maxlength="25" minlength="7"  autocomplete="off"
                                                  value="<?= isset($address) ? ($address['add_Phone']) : '' ?>"   placeholder="Enter Contact Number"  required>
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
											
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">House name / Building No. <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="hname" id="hname" maxlength="30" autocomplete="off"
                                                  value="<?= isset($address) ? ($address['add_BuldingNo']) : '' ?>"   placeholder="Enter housename/building No."  required>
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Street / Area<span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="street" id="street" maxlength="30" autocomplete="off"
                                                  value="<?= isset($address) ? ($address['add_Street']) : '' ?>"   placeholder="Enter Street Name"  required>
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Landmark<span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="landmark" id="landmark" maxlength="30" autocomplete="off"
                                                  value="<?= isset($address) ? ($address['add_Landmark']) : '' ?>"   placeholder="Enter Landmark"  required>
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Town / City<span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="city" id="city" maxlength="30" autocomplete="off"
                                                  value="<?= isset($address) ? ($address['add_City']) : '' ?>"   placeholder="Enter City/Town"  required>
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Pincode<span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="pincode" id="pincode" maxlength="10"  autocomplete="off"
                                                  value="<?= isset($address) ? ($address['add_Pincode']) : '' ?>"   placeholder="Enter Pincode"  required>
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">State<span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="state" id="state" maxlength="20" autocomplete="off"
                                                  value="<?= isset($address) ? ($address['add_State']) : '' ?>"   placeholder="Enter State"  required>
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
                                        </div>
										 <div class="row justify-content-center">
										 
										 <input type="hidden" name ="cust_id" value="<?= $add_CustId; ?>">
										<input type="hidden" name="add_id" value="<?= isset($address['add_Id']) ? esc($address['add_Id']) : '' ?>">
										
                                            <div class="button-group">
											<button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('admin/customer/location/' .$add_CustId); ?>'">
												<i class="bi bi-x-circle"></i> Discard
											</button>
													<button type="button" class="btn btn-primary" id="custSubmit" name="custSubmit">
														<i class="bi bi-check-circle"></i> 
														<?= isset($address['add_Id']) && !empty($address['add_Id']) ? 'Update' : 'Save'; ?>
													</button>	
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page-body end -->
            </div>
            <div id="styleSelector"> </div>
        </div>
    </div>
</div>