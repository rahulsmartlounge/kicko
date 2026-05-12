<!-- <section class="footerbanner" style="background-image:url('<?php echo base_url() . ASSET_PATH; ?>assets/images/footerbanner.png')">
            <div class="container-lg">
                <div class="row">
                    <div class="col-md-6 leftbox">
                        New<br/>Style
                    </div>
                    <div class="col-md-6 rightbox">
                        <div class="yearblock">2025</div>
                        <button class="btn btn-black">View Collection</button>
                    </div>
                </div>
            </div>
        </section> -->
<?php
$footerImage = base_url(ASSET_PATH . 'assets/images/footerbanner.png');
$footerTitle = '';
$footerLink = '#';
$showFooter = false;


if (!empty($themes['theme_Section3'])) {
    $section3 = json_decode($themes['theme_Section3'], true);

    // Filter only entries that have at least one value
    $validSections = array_filter($section3, function ($item) {
        return !empty($item['image']) || !empty($item['name']) || !empty($item['link']);
    });

    if (!empty($validSections)) {
        $validSections = array_values($validSections); // reindex

        // Rotate every 10 minutes based on current time
        $index = floor(time() / 600) % count($validSections);
        $current = $validSections[$index];

        $showFooter = true;

        if (!empty($current['image'])) {
            $footerImage = base_url('public/uploads/themes/' . $current['image']);
        }
        if (!empty($current['name'])) {
          $footerTitle = str_replace(' ', '<br>', esc($current['name']));

        }
        if (!empty($current['link'])) {
            $footerLink = esc($current['link']);
        }
    }
}
?>

<?php if ($showFooter): ?>
  <div id="footerBannerWrapper" style="cursor:pointer;">
    <section class="footerbanner banner-btm-spce" id="footerBanner" style="background-image:url('<?= $footerImage; ?>'); background-size:cover; background-position:center;">
        <div class="container-lg">
            <div class="row">
              <div class="col-md-6 leftbox" id="footerTitle">
    <?= str_replace(' ', '<br>', $footerTitle) ?>
</div>

                <div class="col-md-6 rightbox">
                    <div class="yearblock"><?= date('Y') ?></div>
                    <a href="<?= base_url('product/viewcollection') ?>" class="btn btn-black">View Collection</a>
                </div>
            </div>
        </div>
    </section>
</div>


   <script>
    const footerBanners = <?= json_encode($validSections); ?>;
    let footerIndex = 0;

    const footerLinkTag = document.getElementById('footerBannerWrapper');

    function updateFooterBanner() {
        if (!footerBanners.length) return;

        const item = footerBanners[footerIndex];

        // Update background image
        document.getElementById('footerBanner').style.backgroundImage =
            `url('<?= base_url('public/uploads/themes/') ?>${item.image}')`;

        // Update title
      document.getElementById('footerTitle').innerHTML = item.name ? item.name.replace(/ /g, '<br>') : '';


        // Set click event to redirect
        footerLinkTag.onclick = () => {
            if (item.link) {
                window.location.href = item.link;
            }
        };

        footerIndex = (footerIndex + 1) % footerBanners.length;
    }

    updateFooterBanner(); // First load
    setInterval(updateFooterBanner, 5000); // Rotate every 5 seconds
    document.getElementById('footerBanner').addEventListener('contextmenu', function(e) {
    e.preventDefault();
});
</script>

<?php endif; ?>
