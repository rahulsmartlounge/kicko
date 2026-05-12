<?php if (!empty($product)): ?>
<div class="row product-batch" data-batch="<?= esc($currentBatch ?? 1) ?>">
  <?php foreach ($product as $item): ?>
    <?php
      $images = json_decode($item['product_images'], true);
      $firstImage = $images[0]['name'][0] ?? 'default.jpg';
      $avg = isset($item['ratings']) ? round($item['ratings']) : 0;
    ?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
      <div class="product-item text-center border p-3 h-100">
        <a href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>">
          <img class="product-img img-fluid mb-2"
               src="<?= base_url('uploads/productmedia/') . $firstImage; ?>"
               alt="<?= esc($item['pr_Name']); ?>" />
        </a>

        <div class="star-rate p-1">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="<?= $i <= $avg ? 'bi bi-star-fill gold' : 'bi bi-star' ?>"></i>
          <?php endfor; ?>
        </div>
<a style="text-decoration:none; color:black;" href="<?= base_url('product/product_details/' . $item['pr_Id']); ?>">
    <div class="item-name p-1" data-id="<?= $item['pr_Id'] ?>"><?= esc($item['pr_Name']); ?></div>
</a>

        <div class="item-price">
          <?php if (!empty($item['pr_Discount_Value']) && $item['pr_Discount_Value'] > 0): ?>
            <span style="color: #999;">
              <del><i class="bi bi-currency-rupee"></i><?= intval($item['mrp']); ?></del>
            </span>&nbsp;
            <span>
              <i class="bi bi-currency-rupee"></i><?= intval($item['pr_Selling_Price']); ?>
            </span>
          <?php else: ?>
            <span>
              <i class="bi bi-currency-rupee"></i><?= intval($item['pr_Selling_Price']); ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="text-center mt-2">
          <button class="order-btn"
                  onclick="window.location.href='<?= base_url('product/product_details/' . $item['pr_Id']); ?>'">
           
          </button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
