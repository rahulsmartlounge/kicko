<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10"><?= isset($cust) ? 'Update Customer' : 'Add Customer'; ?></h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('admin/dashboard') ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#"><?= isset($cust) ? 'Update Customer' : 'Add Customer'; ?></a>
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
                                    <form name="createcust" id="createcust" method="post">
                                        <div class="form-group row">
                                           <label class="col-sm-2 col-form-label">Name <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="custname" id="custname" class="form-control" maxlength="30"
                                                    value="<?= isset($cust) ? ($cust['cust_Name']) : '' ?>" placeholder="Enter the customer name" *>
											<span class="text-danger error-msg" id="error-custname"></span>
											</div>
											
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Email <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="email" class="form-control" name="custemail" id="custemail" maxlength="30"
                                                  value="<?= isset($cust) ? ($cust['cust_Email']) : '' ?>"   placeholder="Enter the mail id"  required autocomplete="off">
                                            <span class="text-danger error-msg" id="error-custemail"></span>
											</div>
											
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Contact Number <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                              <input type="tel" class="form-control" name="mobile" id="mobile"  
                                                    pattern="^[0-9+\s\-\(\)]{7,25}$" 
                                                    inputmode="tel" maxlength="25" minlength="7"
                                                    value="<?= isset($cust) ? esc($cust['cust_Phone']) : '' ?>"  
                                                    placeholder="Enter Contact Number" required>
											</div>								
                                        </div>

                                        
										<?php if (empty($cust)) : ?>
										 <!-- Default Add Password Fields -->
                                        <!-- Password Field -->
                                        <!-- Password Field -->
                                <!-- Password Field -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Password <span style="color: red;">*</span></label>
                                        <div class="col-sm-6 position-relative">
                                            <input type="password" class="form-control" name="userpassword" id="userpassword"
                                                placeholder="Enter password" required autocomplete="off">
                                            <span class="text-danger error-msg" id="error-password"></span>
                                            <i class="fa fa-eye-slash position-absolute toggle-password"
                                            style="top: 50%; right: 20px; transform: translateY(-50%); cursor: pointer;"
                                            onclick="togglePassword('userpassword', this)"></i>
                                        </div>
                                    </div>

                                    <!-- Confirm Password Field -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Confirm Password <span style="color: red;">*</span></label>
                                        <div class="col-sm-6 position-relative">
                                            <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                                                placeholder="Confirm password" required autocomplete="off">
                                            <span class="text-danger error-msg" id="error-confirm-password"></span>
                                            <i class="fa fa-eye-slash position-absolute toggle-password"
                                            style="top: 50%; right: 20px; transform: translateY(-50%); cursor: pointer;"
                                            onclick="togglePassword('confirm_password', this)"></i>
                                        </div>
                                    </div>
										<?php endif ?>
										 <div class="row justify-content-center">
										<input type="hidden" name="cust_id" value="<?= isset($cust['cust_Id']) ? esc($cust['cust_Id']) : '' ?>">
                                            <div class="button-group">
											 <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('admin/customer'); ?>'">
                                                    <i class="bi bi-x-circle"></i> Discard
                                                </button>
													<button type="button" class="btn btn-primary" id="custSubmit" name="custSubmit">
														<i class="bi bi-check-circle"></i> 
														<?= isset($cust['cust_Id']) && !empty($cust['cust_Id']) ? 'Update' : 'Save'; ?>
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