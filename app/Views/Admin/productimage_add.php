<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Add Product Image</h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="index.html"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Add Product Image</a>
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

                                        <div id="messageBox" class="alert alert-success" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="card-block">

                                    <form name="createProductImage" id="createProductImage" method="post"
                                        action="<?= base_url('productimage/save') ?>" enctype="multipart/form-data">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Product Name <span
                                                    style="color: red;">*</span></label>
                                            <div class="col-sm-7">
                                                <select class="form-control fs-13" name="pr_id" id="productName"
                                                    required>
                                                    <option value="">-- Select Product--</option>
                                                    <?php if (!empty($products)) : ?>
                                                    <?php foreach ($products as $product): ?>
                                                    <option value="<?= esc($product->pr_Id); ?>">
                                                        <?= esc($product->pr_Name); ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                    <?php else : ?>
                                                    <option value="">No Products Available</option>
                                                    <?php endif; ?>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Product File Type <span
                                                    style="color: red;">*</span></label>
                                            <div class="col-sm-7">
                                                <select class="form-control fs-13" name="file_type" id="fileType"
                                                    required>
                                                    <option value="">-- Select File Type --</option>
                                                    <option value="image">
                                                        Image</option>
                                                    <option value="video">
                                                        Video</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Upload the Product Image<span
                                                    style="color: red;">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="file" class="form-control" name="media_files[]"
                                                    id="media_files" multiple required>
                                            </div>

                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-7 offset-sm-3" id="imagePreview"
                                                style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                                        </div>



                                        <div class="row justify-content-center">
                                            <input type="hidden" name="pri_id">
                                            <div class="button-group">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="window.location.href='<?= base_url('productimage'); ?>'">
                                                    <i class="bi bi-x-circle"></i> Discard
                                                </button>
                                                <button type="button" class="btn btn-primary" id="productimageSubmit"
                                                    name="productimageSubmit">
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


            </div>
            <!-- Page-body end -->
        </div>
        <div id="styleSelector"> </div>
    </div>
</div>
</div>