<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">View Product</h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('admin/dashboard'); ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">View Product</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page-header end -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-100  width: 102%; max-width: 111%;">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white text-dark border-bottom">
                        <h4 class="mb-0"><i class="bi bi-box-seam"></i> <?= esc($product->pr_Name); ?></h4>
                    </div>
                    <div class="card-body p-3">
                        <div id="messageBox" class="alert alert-success d-none"></div>

                        <div class="row">
                            <div class="col-md-6 mb-4">

                                <p style="font-size: 15px;"><strong>Code:</strong> <?= esc($product->pr_Code); ?></p>
                                <p style="font-size: 14px;">
                                    <strong>Description:</strong>
                                    <?= ucwords(strtolower(esc($product->pr_Description))); ?>
                                </p>
                                <p style="font-size: 14px;"><strong>Category:</strong> <?= esc($product->cat_Name); ?>
                                </p>
                                <p style="font-size: 14px;"><strong>Subcategory:</strong>
                                    <?= esc($product->sub_Category_Name); ?></p>
                                <p style="font-size: 14px;"><strong>Selling Price:</strong>
                                    ₹<?= esc($product->pr_Selling_Price); ?></p>
                                <p style="font-size: 14px;"><strong>MRP:</strong> ₹<?= esc($product->mrp); ?></p>
                            </div>

                            <div class="col-md-6 mb-3">

                                <p style="font-size: 14px;"><strong>Stock:</strong> <?= esc($product->pr_Stock); ?></p>
                                <p style="font-size: 14px;"><strong>Discount:</strong>
                                    <?= esc($product->pr_Discount_Value); ?>
                                    <?= esc($product->pr_Discount_Type); ?>
                                </p>
                                <p style="font-size: 14px;"><strong>Available Colors:</strong>
                                    <?php
                                    $colors = explode(',', $product->pr_Aval_Colors);
                                    foreach ($colors as $color):
                                        ?>
                                        <span class="badge badge-info me-1"><?= esc(trim($color)); ?></span>
                                    <?php endforeach; ?>
                                </p>
                                <p style="font-size: 14px;"><strong>Size:</strong>
                                    <?php
                                    $sizes = explode(',', $product->pr_Size);
                                    foreach ($sizes as $size):
                                        ?>
                                        <span class="d-inline-flex justify-content-center align-items-center rounded-circle border bg-light text-dark me-2
                                                                               text-center"
                                            style="width: 43px; height: 40px; font-size: 10px; line-height:1px; white-space: normal ">
                                            <?= esc(trim($size)); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </p>
                                <p style="font-size: 14px;"><strong>Sleeve Style:</strong>
                                    <?= esc($product->pr_Sleeve_Style); ?></p>
                                <p style="font-size: 14px;"><strong>Fabric:</strong> <?= esc($product->pr_Fabric); ?>
                                </p>
                                <p style="font-size: 14px;"><strong>Stitch Type:</strong>
                                    <?= esc($product->pr_Stitch_Type); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-light">
                        <a href="<?= base_url('admin/product'); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>