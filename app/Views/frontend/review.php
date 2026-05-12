<div class="container mt-5">
    <h2>Leave a Review</h2>
    <form id="reviewForm" method="post">
        <div id="reviewResponse" class="mt-3"></div>
        <input type="hidden" name="pr_Id" value="<?= esc($product['pr_Id'] ?? '') ?>" />
        <input type="hidden" name="cust_Id" value="<?= esc($customer['cust_Id'] ?? '') ?>" />


     <div class="form-group col-md-6">
    <label>Name</label>
    <input name="name" class="form-control" value="<?= esc($customer['cust_Name'] ?? '') ?>" readonly />
</div>

<div class="form-group col-md-6">
    <label>Email</label>
    <input name="email" class="form-control" value="<?= esc($customer['cust_Email'] ?? '') ?>" readonly />
</div>


        <div class="form-group col-md-6">
            <label>Rating</label>
            <div id="starRating" class="text-warning" style="font-size: 1.5em; cursor: pointer;">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi bi-star" data-value="<?= $i ?>"></i>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="ratingInput" required>
        </div>
        <div class="form-group col-md-6">
            <label>Review</label>
            <textarea name="review" id="review" class="form-control" ></textarea>
        </div>
        <div class="form-group col-md-6">
            <div> &nbsp;</div>
            <div class="text-end">
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </div>

    </form>
    <h3 class="mt-4 mb-3">Customer Ratings and Reviews</h3>

    <div class="row">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $rev): ?>
                <div class="col-md-6">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-1"><?= esc($rev['name']) ?></h5>
                            <div class="mb-2 text-warning" style="font-size: 1.2em;">
                                <?= str_repeat('★', (int) $rev['rating']) . str_repeat('☆', 5 - (int) $rev['rating']) ?>
                            </div>
                           <?php
                            $review = esc($rev['review']);
                            $maxLength = 150; // Set max characters before trimming
                    
                            if (mb_strlen($review) > $maxLength) {
                                $shortText = esc(mb_substr($review, 0, $maxLength)) . '...';
                                ?>
                                <div class="card-text">
                                    <span class="short-review"><?= $shortText ?></span>
                                    <span class="full-review d-none"><?= $review ?></span>
                                    <a href="javascript:void(0);" class="read-toggle text-primary">Read More</a>
                                </div>
                            <?php } else { ?>
                                <div class="card-text"><?= $review ?></div>
                            <?php } ?>
                            <p class="text-muted mb-0" style="font-size: 0.9em;">
                                <?= date('F d, Y', strtotime($rev['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-muted">No reviews yet.</p>
            </div>
        <?php endif; ?>
    </div>


</div>