<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <!-- <h5 class="m-b-10">Add Category</h5> -->
                        <h5 class="m-b-10"><?= isset($category) ? 'Update Category' : 'Add Category'; ?></h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                             <a href="<?= base_url('admin/dashboard'); ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <!-- <li class="breadcrumb-item"><a href="#!">Add Category</a> -->
                        <li class="breadcrumb-item"><a
                                href="#"><?= isset($category) ? 'Update Category' : 'Add Category'; ?></a>
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
                                        <div id="messageBox" class="alert alert-success" style="display: none;">
                                            </div>
                                        
                                    
                                    </div>
                                </div>
                                <div class="card-block">

                                    <form name="createCategory" id="createCategory" method="post">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Category Name <span
                                                    style="color: red;">*</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="category_name" id="categoryName"
                                                    class="form-control"
                                                    value="<?= isset($category) ? ($category['cat_Name']) : '' ?>"
                                                    placeholder="Enter the Category name">
                                            </div>
                                           
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Discount Type </label>
                                            <div class="col-sm-6">
                                                <select class="form-control fs-13" name="discount_type"
                                                    id="discountType" required>
                                                    <option value="">-- Select Discount Type --</option>
                                                    <option value="%"
                                                        <?= (isset($category) && $category['cat_Discount_Type'] == '%') ? 'selected' : '' ?>>
                                                        %</option>
                                                    <option value="Rs"
                                                        <?= (isset($category) && $category['cat_Discount_Type'] == 'Rs') ? 'selected' : '' ?>>
                                                        Rs</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Discount Value </label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="discount_value"
                                                    id="discountValue"
                                                    value="<?= isset($category) ? ($category['cat_Discount_Value']) : '' ?>"
                                                    placeholder="Enter the Discount value">
                                            </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <input type="hidden" name="cat_id"
                                                value="<?= isset($category['cat_Id']) ? esc($category['cat_Id']) : '' ?>">
                                            <div class="button-group">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="window.location.href='<?= base_url('admin/category'); ?>'">
                                                    <i class="bi bi-x-circle"></i> Discard
                                                </button>
                                                <!-- <button type="button" class="btn btn-primary" id="categorySubmit" name="categorySubmit">
                                                    <i class="bi bi-check-circle"></i> Save
                                                </button> -->
                                                <button type="button" class="btn btn-primary" id="categorySubmit"
                                                    name="categorySubmit">
                                                    <i class="bi bi-check-circle"></i>
                                                    <?= isset($category) ? 'Update' : 'Save'; ?>
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