

<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Update Stock</h5>
                        <p class="m-b-0">Manage product stock for: <?= esc($product['pr_Name']) ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('admin/dashboard'); ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/product'); ?>">Product</a></li>
                        <li class="breadcrumb-item"><a href="#">Update Stock</a></li>
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
                                    <h5>Update Stock for: <?= esc($product['pr_Name']) ?></h5>
                                </div>
                                <div class="card-block">
                                    <div id="messageBox" style="display: none;" class="alert"></div>

                                    <form id="updateStockForm" method="POST">
                                        <div class="form-group row">
                                            <label for="pr_Stock" class="col-sm-2 col-form-label">Stock</label>
                                            <div class="col-sm-6">
                                                <input type="number" name="pr_Stock" id="pr_Stock" class="form-control"
                                                    value="<?= esc($product['pr_Stock']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pr_Reset_stock" class="col-sm-2 col-form-label">Reset Stock</label>
                                            <div class="col-sm-6">
                                                <input type="number" name="pr_Reset_stock" id="pr_Reset_stock"
                                                    class="form-control" value="<?= esc($product['pr_Reset_Stock']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-12 d-flex justify-content-end">
											<input type="hidden" name="pr_Id" id="pr_Id" value="<?= isset($product) ? $product['pr_Id'] : '' ?>">

                                                <a href="<?= base_url('admin/product/edit/'.$product['pr_Id']); ?>" class="btn btn-secondary mr-2">
                                                    <i class="bi bi-x-circle"></i>Discard
                                                </a>
                                              
												<button type="button" class="btn btn-primary" id="updateSubmit"
                                                    name="updateSubmit">
                                                    <i class="bi bi-check-circle"></i>
                                                    Update Stock
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
        </div>
    </div>
</div>