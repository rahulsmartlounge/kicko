<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Manage Customer</h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('admin/dashboard'); ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Customer</a>
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
                        <div class="col-sm-12">

                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-2">

                                        </div>
                                        <div class="col-md-7">

                                        </div>
										<div class="col-md-3">
                                            <div class="row">
                                                <div class="col-lg-12 d-flex justify-content-end p-2">
                                                    <a href="<?= base_url('admin/customer/view/'); ?>" class="btn btn-primary">
                                                        Add Customer
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="card-block">
                                <div class="card">
                                            <div class="card-block table-border-style">
											<div id="message" class="alert" style="display:none;"></div>
											<div id="messageBox" class="alert" style="display: none;"></div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover" id="customerList">
                                                        <thead>
                                                            <tr>
                                                                <th>Sl.No.</th>
                                                                <th>Name</th>
																<th>Email</th>
																<th>Contact Number</th>
																<th>Status</th>
																<th>Action</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
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
