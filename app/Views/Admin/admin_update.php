<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">My Profile</h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('dashboard') ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">My Profile</a>
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
                                    <form name="createstaff" id="createstaff" method="post"  autocomplete="off">
									
                                        <div class="form-group row">
                                           <label class="col-sm-2 col-form-label">Name <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="staffname" id="staffname" class="form-control" maxlength=30
                                                    value="<?= isset($staff) ? ($staff['us_Name']) : '' ?>" placeholder="Enter the staff name" * required >
											<span class="text-danger error-msg" id="error-staffname"></span>
											</div>
											
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Email <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="email" class="form-control" name="staffemail" id="staffemail" maxlength=30
                                                  value="<?= isset($staff) ? esc($staff['us_Email']) : '' ?>"   placeholder="Enter the mail id"  required >
                                            <span class="text-danger error-msg" id="error-staffemail"></span>
											</div>
											
                                        </div>
										 <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Alternate Email</label>
                                            <div class="col-sm-6">
                                                <input type="email" class="form-control" name="staffotemail" id="staffotemail" maxlength=30
                                                  value="<?= isset($staff) ? ($staff['us_Email2']) : '' ?>"   placeholder="Enter alternate mail id">
                                            <span class="text-danger error-msg" id="error-staffotemail"></span>
											</div>
											
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Contact Number <span style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="mobile" id="mobile" maxlength="10" minlength="10"
                                                  value="<?= isset($staff) ? ($staff['us_Phone']) : '' ?>"   placeholder="Enter Contact Number" required autocomplete="off">
                                            <span class="text-danger error-msg" id="error-mobile"></span>
											</div>
											
                                        </div>
                                        <h5 class="mb-3">Change Password</h5>
										<!-- Old Password Field -->
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Old Password </label>
											<div class="col-sm-6">
												<input type="password" class="form-control" name="old_password"
													id="old_password" placeholder="Enter old password" required autocomplete="off">
												<span class="text-danger error-msg" id="error-old-password"></span>
											</div>
										</div>

										<!-- New Password Field -->
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">New Password </label>
											<div class="col-sm-6">
												<input type="password" class="form-control" name="new_password"
													id="new_password" placeholder="Enter new password" required>
												<span class="text-danger error-msg" id="error-new-password"></span>
											</div>
										</div>
                                        <div class="row justify-content-center">
										<input type="hidden" name="us_id" value="<?= isset($staff['us_Id']) ? esc($staff['us_Id']) : '' ?>">
                                            <div class="button-group">
											 <button type="button" class="btn btn-secondary" onclick="window.location.href='<?= base_url('dashboard'); ?>'">
                                                    <i class="bi bi-x-circle"></i> Discard
                                                </button>
													<button type="button" class="btn btn-primary" id="staffSubmit" name="staffSubmit" >
														<i class="bi bi-check-circle"></i> Save
														
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