<?php
$latestProducts = $product ?? [];
$first20 = array_slice($latestProducts, 0, 20);
$next20 = array_slice($latestProducts, 20, 20);
?>

<section class="top-prod" id="top-products">
    <div class="container-lg">
        <div class="col-md-12">
            <div class="row">
                <h3>Top Products</h3>
            </div>

            <!-- First 20 products -->
            <div class="row mb-4">
                <div class="owl-carousel" id="top-prod-owl">
                    <?php foreach ($first20 as $item): ?>
                        <?php
                        $images = json_decode($item['product_images'], true);
                        $firstImage = isset($images[0]['name'][0]) ? $images[0]['name'][0] : 'default.jpg';
                        ?>
                        <div class="item">
                            <div class="col-md-12">
                                <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>">
                                    <img class="product-img" src="<?= base_url('uploads/productmedia/' . $firstImage); ?>"
                                        alt="<?= esc($item['pr_Name']); ?>" />
                                </a>
                            </div>
                            <div class="star-rate p-1">
                                <?php
                                $avg = intval($item['avg_rating'] ?? 0);
                                for ($i = 1; $i <= 5; $i++):
                                    ?>
                                    <i class="<?= $i <= $avg ? 'bi bi-star-fill gold' : 'bi bi-star' ?>"></i>
                                <?php endfor; ?>
                            </div>

                            <div class="item-name p-1" title="<?= esc($item['pr_Name']); ?>">
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
                                        <i class="bi bi-currency-rupee"></i><?= esc(round($item['pr_Selling_Price'])); ?>
                                    </span>
                                <?php else: ?>
                                    <!-- Only Selling Price -->
                                    <span>
                                        <i class="bi bi-currency-rupee"></i><?= esc(round($item['pr_Selling_Price'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12 text-center">
                                <button class="order-btn"
                                    onclick="window.location.href='<?= base_url('product/product_details/' . $item['pr_Id']); ?>'"></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Next 20 products -->
            <?php if (count($latestProducts) > 20): ?>
                <div class="row">
                    <div class="owl-carousel" id="top-prod-owl-two">
                        <?php foreach ($next20 as $item): ?>
                            <?php
                            $images = json_decode($item['product_images'], true);
                            $firstImage = isset($images[0]['name'][0]) ? $images[0]['name'][0] : 'default.jpg';
                            ?>
                            <div class="item">
                                <div class="col-md-12">
                                    <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>">
                                        <img class="product-img" src="<?= base_url('uploads/productmedia/' . $firstImage); ?>"
                                            alt="<?= esc($item['pr_Name']); ?>" />
                                    </a>
                                </div>
                                <div class="star-rate p-1">
                                    <?php
                                    $avg = intval($item['avg_rating'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++):
                                        echo '<i class="' . ($i <= $avg ? 'bi bi-star-fill gold' : 'bi bi-star') . '"></i>';
                                    endfor;
                                    ?>
                                </div>
                                <div class="item-name p-1">
                                    <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>"
                                        class="text-dark text-decoration-none">
                                        <?= esc($item['pr_Name']); ?>
                                    </a>
                                </div>
                                <div class="item-price"><i
                                        class="bi bi-currency-rupee"></i>&nbsp;<?= esc(round($item['pr_Selling_Price'])); ?>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button class="order-btn"
                                        onclick="window.location.href='<?= base_url('product/product_details/' . $item['pr_Id']); ?>'"></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>



<script>
    $(document).ready(function () {
        $('#top-prod-owl, #top-prod-owl-two').owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                },
                1200: {
                    items: 5
                }
            }
        });
             // ✅ Disable right-click on all images
        $('body').on('contextmenu', 'img', function (e) {
            return false;
        });

        // ✅ Optional: Disable drag-save
        $('body').on('mouseenter', 'img', function () {
            $(this).attr('draggable', false);
        });
    });

   
</script>