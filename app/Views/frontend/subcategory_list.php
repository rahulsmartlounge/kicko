<?php if (isset($subcat_id)): ?>

    <section class="top-prod">
        <div class="container-lg">
            <div class="row">
                <div class="col-12 " Style="padding:10px;">
                    <h3 class="heading-left" Style="padding-left:2px;"><?= esc($subcat_id['sub_Category_Name']) ?></h3>
                </div>
            </div>
            <?php if ($subcat_id): ?>
             

                 <div class="row" id="product-container">
               <?= view('product/_subcategory_product_items', ['product' => $product]); ?>

            </div>
          

            <!-- Load More Button -->
      
            <?php else: ?>
                <div class="alert alert-warning text-center">No Sub Category found.</div>
            <?php endif; ?>
            <!-- //****************************************************************************************************************// -->

            <?php if (isset($similar)): ?>
                <?php if (!empty($similar)): ?>
                    <div class="row">
                        <div class="col-12 " Style="padding: 0px 12px;">
                            <h3><br />SIMILAR PRODUCTS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="swiper mySwiper" style="padding: 0;">
                            <div class="swiper-button-next"></div>
                            <div class=" swiper-wrapper">
                                <?php
                                $uniqueIds = [];

                                foreach ($similar as $item):
                                    if (in_array($item['pr_Id'], $uniqueIds)) {
                                        continue;
                                    }
                                    $uniqueIds[] = $item['pr_Id'];
                                    $images = json_decode($item['product_images'], true);
                                    $firstImage = isset($images[0]['name'][0]) ? $images[0]['name'][0] : 'default.jpg';
                                    ?>
                                    <div class="swiper-slide" style="width: 100%; ">
                                        <div class=" text-center" style="">
                                            <div class=" p-2 position-relative" style="overflow: hidden; z-index:2px;">
                                                <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>">
                                                    <img class="product-img img-fluid mb-2"
                                                      
                                                        src="<?= base_url('uploads/productmedia/') . $firstImage; ?>"
                                                        alt="<?= esc($item['pr_Name']); ?>" />
                                                </a>
                                                <div class="star-rate p-1">
                                                    <?php
                                                    $avg = round($item['avg_rating'] ?? 0, 1);
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($avg >= $i) {
                                                            echo '<i class="bi bi-star-fill gold"></i>';
                                                        } elseif ($avg >= ($i - 0.5)) {
                                                            echo '<i class="bi bi-star-half gold"></i>';
                                                        } else {
                                                            echo '<i class="bi bi-star"></i>';
                                                        }
                                                    }
                                                    ?>
                                                    <!-- Show numeric average -->

                                                </div>

                                                <div class="item-name p-1">
                                                    <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>"
                                                        class="text-dark text-decoration-none">
                                                        <?= esc($item['pr_Name']); ?>
                                                    </a>
                                                </div>
                                                <div class="item-price">
                                                    <?php if (!empty($item['pr_Discount_Value']) && $item['pr_Discount_Value'] > 0): ?>
                                                        <!-- MRP with strikethrough -->
                                                        <span style="color: #999;">
                                                            <del>
                                                                <i class="bi bi-currency-rupee"></i><?= esc($item['mrp']); ?>
                                                            </del>
                                                        </span>
                                                        &nbsp;
                                                        <!-- Selling Price -->
                                                        <span>
                                                            <i
                                                                class="bi bi-currency-rupee"></i><?= esc(round($item['pr_Selling_Price'])); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <!-- Only Selling Price -->
                                                        <span>
                                                            <i
                                                                class="bi bi-currency-rupee"></i><?= esc(round($item['pr_Selling_Price'])); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-center mt-2">
                                                    <div class="col-md-12 text-center">
                                                        <button class="order-btn"
                                                            onclick="window.location.href='<?= base_url('product/product_details/' . $item['pr_Id']); ?>'"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <!-- //****************************************************************************************************************// -->

        </div>

    </section>

<?php else: ?>
    <div>Subcategory ID not found.</div>
<?php endif; ?>
