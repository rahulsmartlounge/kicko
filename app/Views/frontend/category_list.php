<section class="top-prod">
    <div class="container-lg">
        <div class="row">
            <div class="col-12 " Style="padding:10px;">
                <h3 class="heading-left" Style="padding-left:2px;">EXPLORE THE FASHION</h3>
            </div>
        </div>

        <?php if (empty($category)): ?>
            
            <div class="alert alert-warning text-center">No Category found.</div>
        <?php else: ?>
            <div class="row">
                <?php
                $uniqueIds = [];

                foreach ($category as $item):
                    if (in_array($item['cat_Id'], $uniqueIds)) {
                        continue;
                    }
                    $uniqueIds[] = $item['cat_Id'];
                    $images = json_decode($item['product_images'], true);
                    $firstImage = isset($images[0]['name'][0]) ? $images[0]['name'][0] : 'default.jpg';
                    ?>
                    <div class="col-md-3 mb-3">
                        <a href="<?= base_url('category/catProducts/' . $item['cat_Id']); ?>">
                            <div class="card h-100 text-center">
                                <div class="card-body">

                                    <img class="product-img img-fluid mb-2"
                                        src="<?= base_url('uploads/productmedia/') . $firstImage; ?>"
                                        alt="<?= esc($item['cat_Name']); ?>" />

                                    <h5><?= esc($item['cat_Name']) ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>


<!-- Load More Button -->
<div class="text-center mt-3">
    <button id="load-more" data-page="2" data-cat-id="<?= $category_id ?>" class="btn btn-primary">Load More</button>
    <div id="no-more-products" class="d-none text-center mt-3">No More Categories</div>
</div>

        <?php endif; ?>
    </div>
</section>