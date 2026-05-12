<!-- <section class="category-promo">
    <div class="container-lg">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4">
                    <img src="<?php echo base_url().ASSET_PATH; ?>assets/images/c1.jpg" />
                    <div class="col-md-12 cat-title">New Women Style</div>
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url().ASSET_PATH; ?>assets/images/c2.jpg" />
                    <div class="col-md-12 cat-title">Best Women Shopping</div>
                </div>
                <div class="col-md-4">
                    <img src="<?php echo base_url().ASSET_PATH; ?>assets/images/c3.jpg" />
                    <div class="col-md-12 cat-title">Top Women Collection</div>
                </div>
            </div>
        </div>
    </div>
</section> -->

<section class="category-promo">
    <div class="container-lg">
        <div class="col-md-12">
            <div class="row" id="section2-slider">
                <?php
                if (!empty($themes['theme_Section2'])):
                    $section2 = json_decode($themes['theme_Section2'], true);
                    foreach ($section2 as $index => $item):
                        if (!empty($item['image'])):
                            $imagePath = base_url('public/uploads/themes/' . $item['image']);
                            $title = !empty($item['name']) ? $item['name'] : '';
                            $link = !empty($item['link']) ? $item['link'] : '#';
                ?>
                <div class="col-md-4 section2-item" data-index="<?= $index ?>" style="<?= $index >= 3 ? 'display:none;' : '' ?>">
                    <a href="<?= esc($link) ?>">
                        <img src="<?= esc($imagePath) ?>" class="img-fluid" />
                    </a>
                    <div class="col-md-12 cat-title"><?= esc($title) ?></div>
                </div>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</section>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const allItems = document.querySelectorAll(".section2-item");
    const totalItems = allItems.length;
    let currentIndex = 0;

    function updateSection2() {
        allItems.forEach(item => item.style.display = "none");

        for (let i = 0; i < 3; i++) {
            const index = (currentIndex + i) % totalItems;
            allItems[index].style.display = "block";
        }

        currentIndex = (currentIndex + 3) % totalItems;
    }

    // Refresh every 10 minutes (600000 ms)
    setInterval(updateSection2, 600000);
});
</script>

