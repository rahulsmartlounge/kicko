<?php if (!empty($product)): ?>
    <?php foreach ($product as $item): ?>
        <?php
        $images = json_decode($item['product_images'], true);
        $firstImage = isset($images[0]['name'][0]) ? $images[0]['name'][0] : 'default.jpg';
        ?>
        <div class="col-md-3 mb-3">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>">
                        <img class="product-img img-fluid mb-2"
                            src="<?= base_url('uploads/productmedia/') . $firstImage; ?>"
                            alt="<?= esc($item['pr_Name']); ?>" />
                    </a>
                    <div class="star-rate p-1">
                        <?php
                        $avg = round($item['avg_rating'] ?? 0, 1);
                        for ($i = 1; $i <= 5; $i++) {
                            echo $avg >= $i ? '<i class="bi bi-star-fill gold"></i>' :
                                ($avg >= ($i - 0.5) ? '<i class="bi bi-star-half gold"></i>' : '<i class="bi bi-star"></i>');
                        }
                        ?>
                    </div>
                    <div class="item-name p-1">
                        <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>"
                            class="text-dark text-decoration-none">
                            <?= esc($item['pr_Name']); ?>
                        </a>
                    </div>
                    <div class="item-price">
                        <?php if (!empty($item['pr_Discount_Value']) && $item['pr_Discount_Value'] > 0): ?>
                            <span style="color: #999;"><del><i class="bi bi-currency-rupee"></i><?= esc($item['mrp']); ?></del></span>
                            &nbsp;
                            <span><i class="bi bi-currency-rupee"></i><?= esc(round($item['pr_Selling_Price'])); ?></span>
                        <?php else: ?>
                            <span><i class="bi bi-currency-rupee"></i><?= esc(round($item['pr_Selling_Price'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="text-center mt-2">
                        <button class="order-btn"
                            onclick="window.location.href='<?= base_url('product/product_details/' . $item['pr_Id']); ?>'"></button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12">
        <div class="alert alert-warning text-center">No more products.</div>
    </div>
<?php endif; ?>
