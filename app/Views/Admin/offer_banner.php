<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Offer Banners</h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="index.html"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Offer Banners</a>
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
                                                    <a href="<?= base_url('offer_banner/add'); ?>"
                                                        class="btn btn-primary">
                                                        Add Offer Banners
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="card">

                                        <div class="card-block table-border-style">
										<div id="message" style="display:none;"></div>
											<div id="messageBox" class="alert" style="display: none;"></div>
                                            <div class="table-responsive">
                                               <table class="table table-hover" id="productList" style="width:100%">
													<thead>
														<tr>
															<th>#</th>
															<th>Name</th>
															<th>Category</th>
															<th>Subcategory</th>
															<th>Product</th>
															<th>Image</th>
															<th>Status</th>
															<th>Actions</th>
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

<!--Delete Modal-->

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <img src="<?= base_url('public/assets/images/delete_icon.gif'); ?>" alt="Delete Icon"
                            class="img-fluid d-block mx-auto" style="width:100px;">
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <p class="text-center">
                            Are you sure you want to delete the banner?</p>
                    </div>

                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteStaff"
                    onclick="deleteStaff()">Delete </button>
            </div>
        </div>
    </div>
</div>
</div>