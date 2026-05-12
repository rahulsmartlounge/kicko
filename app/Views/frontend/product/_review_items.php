<div>&nbsp;</div><?php if (!empty($reviews)): ?>
    <?php foreach (array_chunk($reviews, 2) as $reviewPair): ?>
        <div class="row mb-4">
            <?php foreach ($reviewPair as $rev): ?>
                <div class="col-md-6">
                    <h6 class="card-title mb-1" title="<?= esc($rev['name']) ?>">
                        <?= mb_strlen($rev['name']) > 25 ? esc(mb_substr($rev['name'], 0, 25)) . '...' : esc($rev['name']) ?>
                    </h6>
                    <div class="mb-2 text-warning" style="font-size: 1.2em;">
                        <?= str_repeat('★', (int) $rev['rating']) . str_repeat('☆', 5 - (int) $rev['rating']) ?>
                    </div>
                    <p class="card-text">
                        <?php if (strlen($rev['review']) > 50): ?>
                            <span class="short-text"><?= esc(substr($rev['review'], 0, 50)) ?>...</span>
                            <span class="full-text d-none"><?= esc($rev['review']) ?></span>
                            <a href="javascript:void(0);" class="toggle-review text-primary fw-bold">Read more</a>
                        <?php else: ?>
                            <?= esc($rev['review']) ?>
                        <?php endif; ?>
                    </p>
                    <span style="font-size:12px; color:#000;">Posted on
                        <?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12">
        <p class="text-muted">No reviews yet.</p>
    </div>
<?php endif; ?>