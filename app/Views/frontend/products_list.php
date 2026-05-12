<section class="top-prod">
  <div class="container-lg">
    <div class="row">
      <div class="col-12" style="padding:10px;">
        <h3 class="heading-left" style="padding-left:2px;">PRODUCTS</h3>
      </div>
    </div>

    <?php if (empty($product)): ?>
      <div class="alert alert-warning text-center">No products found.</div>
    <?php else: ?>
      <!-- Product grid -->
      <div class="row" id="product-list">
        <?= view('product/_product_items', ['product' => $product]) ?>
      </div>

      <!-- Load More button -->
      <div class="row">
        <div class="col-12 text-center">
          <button id="load-more" class="btn btn-primary" data-page="2" data-keyword="<?= esc($keyword ?? '') ?>"
            data-has-more="true">
            <i class="bi bi-arrow-down-circle" style="font-size: 1.4rem;"></i>
          </button>
          
          <p id="no-more-products" class="d-none text-muted mt-2 text-center">
            <i class="bi bi-check-circle text-success"></i> No more products.
          </p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>