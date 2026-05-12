<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10"><?= isset($banner) ? 'Update Offer Banner' : 'Add Offer Banner'; ?></h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard'); ?>"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!"><?= isset($banner) ? 'Update Offer Banner' : 'Add Offer Banner'; ?></a>
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
                                    <form name="banner_add" id="banner_add" method="post" enctype="multipart/form-data">
                                       <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Banner File Name<span
                                            style="color: red;">*</span></label>
                                            <div class="col-sm-7">
											<input type="text" name="file_name" class="form-control" placeholder="Enter the Banner Name"
											value="<?= isset($banner['the_Name']) ? esc($banner['the_Name']) : '' ?>" />
                                            </div>
                                        </div>
                                        
										<div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Category Name</label>
                                            <div class="col-sm-7">
											
											<select name="cat_id" id="categoryName" class="form-control">
												<option value="">-- Select Category --</option>
												<?php foreach ($category as $cat): ?>
												<option value="<?= $cat->cat_Id ?>"
													<?= (isset($banner['the_CatId']) && $banner['the_CatId'] == $cat->cat_Id) ? 'selected' : '' ?>>
													<?= esc($cat->cat_Name) ?>
													</option>
												<?php endforeach; ?>
											</select>

                                               
                                            </div>
                                        </div>
										 <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Subcategory Name</label>
                                            <div class="col-sm-7">
											
												<select name="sub_id" id="subcategoryName" class="form-control">
													<option value="">-- Select Subcategory --</option>
													<!-- Options will be loaded dynamically via JS, but you can prefill if editing -->
												</select>
                                                
                                                <small id="noSubcategoryMsg" class="text-danger" style="display: none;">
                                                    This category has no subcategory.
                                                </small>
                                            </div>

                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Product Name</label>
                                            <div class="col-sm-7">
											<select name="pr_id" id="productName" class="form-control">
    <option value="">-- Select Product --</option>
    <!-- Options will be loaded dynamically via JS -->
</select>


                                                <small id="noproductMsg" class="text-danger" style="display: none;">
                                                    This category has no product.
                                                </small>
                                            </div>
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Upload the Image<span
                                            style="color: red;">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="file" name="banner_image" id="banner_image" value="" class="form-control">
                                            <?php if (isset($banner) && !empty($banner['the_Offer_Banner'])): ?>
												<img id="preview"
													 src="<?= base_url('/public/uploads/' . $banner['the_Offer_Banner']); ?>"
													 alt="Image Preview"
													 style="max-height: 50px; margin-top: 10px; display: block;" />
											<?php else: ?>
												<img id="preview"
													 src="#"
													 alt="Image Preview"
													 style="max-height: 50px; margin-top: 10px; display: none;" />
											<?php endif; ?></div>
											
                                        </div>
										<div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Description</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" value="<?= isset($banner['the_Description']) ? esc($banner['the_Description']) : '' ?>" name="description" id="description"
                                                 placeholder="Enter the description">
                                            </div>
                                        </div>
                                        
                                        <div class="row justify-content-center">
                                            <input type="hidden" name="the_id" value="<?= isset($banner['the_Id']) ? esc($banner['the_Id']) : '' ?>">
                                            <div class="button-group">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="window.location.href='<?= base_url('offer_banner'); ?>'">
                                                    <i class="bi bi-x-circle"></i> Discard
                                                </button>
                                                <button type="button" class="btn btn-primary" id="imageSubmit"
                                                    name="imageSubmit">
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