<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Manage Profile</h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('admin/dashboard'); ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Profile</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <!-- Page-header end -->



    <!-- Profile Form Start -->
    <div class="main-body">
        <div class="page-wrapper">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Edit Profile</h5>
                        </div>
                        <div class="card-block">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success" id="tog-alert"><?= session()->getFlashdata('success') ?>
                                </div>
                            <?php elseif (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                            <?php endif; ?>

                            <form method="post" id="updateProfileForm"
                                action="<?= base_url('admin/profile/update'); ?>">

                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="us_Name" class="form-control"
                                        value="<?= esc($user['us_Name'] ?? '') ?>" required>

                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" name="us_Email" class="form-control"
                                        value="<?= esc($user['us_Email'] ?? '') ?>" required>
                                </div>
                                <div class="form-group phn_code ">
                                    <label style="width:100%;">Phone number</label>
                                    <input type="tel" id="phone" name="us_Phone" style="padding-left: 472px;" class="form-control"
                                        value="<?= esc($user['us_Phone'] ?? '') ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="col-sm-6">
                    <div class="card">


                        <div class="card-header">
                            <h5>Change Password</h5>
                        </div>
                        <div class="card-block">
                            <div id="messageBox" class="alert" style="display: none;"></div>
                            <form method="post" id="changePasswordForm"
                                action="<?= base_url('admin/profile/change_password'); ?>">
                                <?php
                                if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('error') ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php
                                endif;
                                ?>
                                <div class="form-group" style="position: relative;">
                                    <label>Current Password <span style="color: red;">*</span></label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control" maxlength="15" minlength="6" placeholder="Current Password"
                                        required>
                                    <i class="toggle-password fa fa-eye-slash position-absolute"
                                        style="top: 45px; right: 10px; cursor: pointer;"
                                        data-target="current_password"></i>
                                </div>
                                <div class="form-group" style="position: relative;">
                                    <label>New Password <span style="color: red;">*</span></label>
                                    <input type="password" name="new_password" id="new_password" class="form-control"
                                        maxlength="15" minlength="6" placeholder="New Password" required>
                                    <i class="toggle-password fa fa-eye-slash  position-absolute"
                                        style="top: 45px; right: 10px; cursor: pointer;" data-target="new_password"></i>
                                </div>

                                <div class="form-group" style="position: relative;">
                                    <label>Confirm New Password <span style="color: red;">*</span></label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="form-control" maxlength="15" minlength="6"
                                        placeholder="Confirm New Password" required>
                                    <i class="toggle-password fa fa-eye-slash position-absolute"
                                        style="top: 45px; right: 10px; cursor: pointer;"
                                        data-target="confirm_password"></i>
                                </div>

                                <button type="button" id="passUpdate" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- End Change Password -->
            </div>
        </div>
    </div>
</div>