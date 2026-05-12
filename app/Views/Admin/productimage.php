<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Product</h5>
                        <p class="m-b-0">Welcome to Kico Admin Control Panel</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="index.html"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#!">Product</a>
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
                                            <div id="message" style="display:none;"></div>
                                            <div id="messageBox" class="alert" style="display: none;"></div>

                                        </div>
                                        <div class="col-md-3">
                                            <div class="row">
                                                <div class="col-lg-12 d-flex justify-content-end p-2">
                                                    <a href="<?= base_url('productimage/add'); ?>"
                                                        class="btn btn-primary">
                                                        Add Product Image
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="card">

                                        <div class="card-block table-border-style">
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="productList">
                                                    <thead>
                                                        <tr>
                                                            <th>Slno</th>
                                                            <th>Product Name</th>
                                                            <th>Thumbnail image</th>
                                                            <th>File Type</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($productimages as $index => $prodimg) : ?>
                                                        <tr>
                                                            <td><?= $index + 1; ?></td>
                                                            <td><?= ucwords($prodimg->pr_Name); ?></td>
                                                            <td>
                                                                <?php 
                                                               $thumbnails = json_decode($prodimg->pri_Thumbnail, true);
                                                                if (!empty($thumbnails)) {
                                                                    foreach ($thumbnails as $thumb) {
                                                                         echo '<img src="' . base_url('uploads/productmedia/' . $thumb['name']) . '" width="80" height="80" style="object-fit: cover; margin-right: 5px;" />';
                                                                        }
                                                                    }
                                                                ?>
                                                            </td>

                                                            <td><?= $prodimg->pri_File_Type; ?></td>
                                                            <td>

                                                            </td>
                                                            <td>


                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>

                                                    </tbody>

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