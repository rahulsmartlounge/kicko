<?php if (isset($cat_id)): ?>
    <section class="top-prod">
        <div class="container-lg">
            <div class="row">
                <div class="col-12" style="padding:10px;">
                    <h3 class="heading-left" style="padding-left:2px;"><?= esc($cat_Name) ?></h3>
                </div>
            </div>

            <?php if (empty($product)): ?>
                <div class="alert alert-warning text-center">No Category found.</div>
            <?php else: ?>

                <!-- Show Subcategories -->
                <?php if (!empty($subcategory)): ?>
                    <div class="swiper mySwiper" style="padding:0px;">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-wrapper">
                            <?php
                            $uniqueIds = [];
                            foreach ($subcategory as $item):
                                if (in_array($item['sub_Id'], $uniqueIds))
                                    continue;
                                $uniqueIds[] = $item['sub_Id'];
                                $images = json_decode($item['product_images'], true);
                                $firstImage = isset($images[0]['name'][0]) ? $images[0]['name'][0] : 'default.jpg';
                                ?>
                                <div class="swiper-slide card-slide" style="width: 120px;">
                                    <a href="<?= base_url('subcategory/subcategoryProducts/' . $item['sub_Id'] . '/' . $cat_id); ?>">
                                        <div class="card text-center" style="height: 210px; max-width: 120px;">
                                            <div class="card-body p-2 position-relative" style="overflow: hidden;">
                                                <img class="product-img img-fluid mb-2"
                                                    src="<?= base_url('uploads/productmedia/') . $firstImage; ?>"
                                                    alt="<?= esc($item['sub_Category_Name']); ?>"
                                                    style="height: 150px; object-fit: cover;" />
                                                <div class="item-name p-1" style="font-size: 11px; word-wrap: break-word;">
                                                    <?= esc($item['sub_Category_Name']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-button-prev"></div>
                    </div>
                <?php endif; ?>

                <br />



                <!-- AJAX Loaded Products -->
                <!-- <div class="row" id="product-container">
        

            </div> -->
                <div class="row" id="product-container">
                    <?= view('product/_cat_product_items', ['product' => $product]); ?>
                    <?= view('pagescripts/category_listjs'); ?>
                </div>


                <!-- Load More Button -->
                <div class="text-center mt-3">
                    <button id="load-more" data-page="2" data-cat-id="<?= $cat_id ?>" class="btn btn-primary">Load More</button>
                    <div id="no-more-products" class="d-none text-center mt-3">No more products</div>
                </div>

            <?php endif; ?>
        </div>
    </section>
<?php else: ?>
    <div>Category ID not found.</div>
<?php endif; ?>
<script>

</script>