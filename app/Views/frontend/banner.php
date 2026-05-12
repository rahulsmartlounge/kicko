<section class="hero-banner">
    <div class="container-lg">
        <div class="row">

            <?php
            $section1Images = [];

            if (!empty($themes['theme_Section1'])) {
                $section1 = json_decode($themes['theme_Section1'], true);

                if (!empty($section1) && is_array($section1)) {
                    foreach ($section1 as $item) {
                        if (!empty($item['image'])) {
                            $section1Images[] = [
                                'image' => base_url('public/uploads/themes/' . $item['image']),
                                'link' => !empty($item['link']) ? esc($item['link']) : '#'
                            ];
                        }
                    }
                }
            }

            if (!empty($section1Images)):
                $firstImage = $section1Images[0];
                ?>
                <div class="col-md-12 banner">
                    <a href="<?= esc($firstImage['link']) ?>" id="theme-banner-link">
                        <img src="<?= esc($firstImage['image']) ?>" alt="Theme Banner" class="img-fluid"
                            id="theme-banner-img">
                    </a>
                </div>

                <script>
                    
                    const section1Images = <?= json_encode($section1Images); ?>;
                    let currentImageIndex = 0;

                    function rotateBannerImage() {
                        currentImageIndex = (currentImageIndex + 1) % section1Images.length;
                        const current = section1Images[currentImageIndex];

                        const bannerImg = document.getElementById('theme-banner-img');
                        const bannerLink = document.getElementById('theme-banner-link');

                        bannerImg.src = current.image;
                        bannerLink.href = current.link;
                    }

                    // Change image every 5 seconds (5000 ms)
                    setInterval(rotateBannerImage, 5000);
                </script>
            <?php endif; ?>

        </div>
        <div class="row">
            <div class="col-md-12 highlightrow">
                <div class="row">
                    <div class="col-md-4 text-center highlights"><i class="bi bi-person-circle"></i>24x7 Free Support
                    </div>
                    <div class="col-md-4 text-center highlights"><i class="bi bi-wallet"></i>Money Back Guarantee</div>
                    <div class="col-md-4 text-center highlights"><i class="bi bi-truck"></i>Free World wide Shipping
                    </div>
                </div>
            </div>
        </div>
    </div>







</section>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const bannerImages = <?= json_encode($section1Images); ?>;
        let currentIndex = 0;

        if (bannerImages.length > 1) {
            setInterval(() => {
                currentIndex = (currentIndex + 1) % bannerImages.length;
                const newImage = bannerImages[currentIndex];
                document.getElementById('theme-banner-img').src = newImage.image;
                document.getElementById('theme-banner-link').href = newImage.link;
            }, 600000); // 10 min = 10000 milliseconds
        }
    });
</script>