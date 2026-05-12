<!DOCTYPE html>
<html>

<head>
    <title>Zakhi Designs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-signin-client_id"
        content="980312560634-kksn59gmuu5p4rg68tnd2vaooe7lfdfu.apps.googleusercontent.com">


    <link rel="stylesheet" href="<?= base_url() . ASSET_PATH; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() . ASSET_PATH; ?>assets/css/customstyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url() . ASSET_PATH; ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?= base_url() . ASSET_PATH; ?>assets/css/custom.css">

    <link rel="stylesheet" href="<?= base_url() . ASSET_PATH; ?>assets/vendors/owlcarousel/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="<?= base_url() . ASSET_PATH; ?>assets/vendors/owlcarousel/assets/owl.theme.default.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Favicon icon -->
    <link rel="icon" href="<?php echo base_url() . ASSET_PATH; ?>assets/images/logo.jpg">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-----------------------------Country code---------------------------------->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18/build/css/intlTelInput.css" />
    



</head>

<body>
    <header>
        <div class="container-lg" style="top:0px;">
            <div class="row head-row">
                <div class="col-6 logo">
                    <a href="<?= base_url(); ?>">
                        <img src="<?= base_url() . ASSET_PATH; ?>assets/images/logo.jpg" alt="Logo" />
                    </a>
                </div>
                <?= view_cell('App\Cells\FooterCell::footerInfo') ?>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <nav class="topnav" id="respTopnav">
                        <a href="<?= base_url(); ?>" class="active">Home</a>
                        <a href="<?= base_url('aboutus'); ?>">About Us</a>
                        <!-- Fashion dropdown -->
                        <div class="dropdown-wrapper a fashion-menu position-relative " style=" cursor: pointer;">
                            <span class="dropbtn">Fashion</span>
                            <div class="cat-dropdown">
                                <?php if (!empty($categories)): ?>
                                    <?php foreach (array_slice($categories, 0, 10) as $category): ?>
                                        <div class="cat-item position-relative">
                                            <a href="<?= base_url('category/catProducts/' . $category['cat_Id']) ?>"
                                                class="cat-link" data-cat-id="<?= $category['cat_Id'] ?>">
                                                <?= esc($category['cat_Name']) ?>
                                            </a>

                                            <?php if (!empty($category['subcategories'])): ?>
                                                <div class="sub-dropdown">
                                                    <?php foreach ($category['subcategories'] as $sub): ?>
                                                        <a
                                                            href="<?= base_url('subcategory/subcategoryProducts/' . $sub['sub_Id'] . '/' . $category['cat_Id']) ?>">
                                                            <?= esc($sub['sub_Category_Name']) ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>

                                <?php endif; ?>
                                <div class="cat-item">
                                    <a href="<?= base_url('category/category_list') ?>">All Category</a>
                                </div>
                            </div>
                        </div>
                        <a href="<?= base_url('contact'); ?>">Contact</a>

                        <?php if (session()->get('zd_uname')):
                            ?>
                            <div class="dropdown a">
                                <div class="dropdown-toggle drop-menu p-0" href="#" role="button" id="customerDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php
                                    $username = session()->get('zd_uname');
                                    echo (strlen($username) > 10) ? substr($username, 0, 10) . '...' : $username;
                                    ?>
                                </div>
                                <ul class="dropdown-menu" aria-labelledby="customerDropdown">
                                    <li>
                                        <a class="dropdown-item small-menu-item" href="<?= base_url('profile#profile'); ?>">
                                            <i class="bi bi-person-circle me-1"></i> My Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small-menu-item" href="<?= base_url('logout') ?>">
                                            <i class="bi bi-escape me-1"></i> Logout
                                        </a>
                                    </li>
                                </ul>

                            </div>

                        <?php else: ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" id="loginBtn">Login</a>

                            <!-- <a href="#" id="registerBtn">Register</a> -->
                        <?php endif; ?>


                        <a href="javascript:void(0);" class="icon" onclick="openRespMenu()">
                            <i class="bi bi-list"></i>
                        </a>
                        <div class="searchbox"
                            style="display: flex; align-items: center; gap: 5px; position: relative; top: -5px;">
                            <input type="text" name="keyword" id="search"
                                placeholder="Search Products/Category/Sub:Cate" autocomplete="off"
                                value="<?= esc($search ?? '') ?>" style="padding: 5px; "
                                onkeydown="checkEnter(event)" />

                            <a href="javascript:void(0);" onclick="searchProduct()"
                                style="text-decoration: none; color: inherit;">
                                <i class="bi bi-search" style="position: relative; top: -6px;"></i>
                            </a>

                        </div>

                    </nav>
                </div>
            </div>
        </div>
    </header>


    </div>
    </div>
    </div>


    <script>
        function searchProduct() {
            const keyword = document.getElementById('search').value.trim();
            if (keyword !== '') {
                window.location.href = "<?= base_url('product/search') ?>?keyword=" + encodeURIComponent(keyword);
            }
        }

        function checkEnter(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchProduct();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            let lastClickedLink = null;
            let tapTimeout;

            const hideAllDropdowns = () => {
                document.querySelectorAll('.sub-dropdown').forEach(drop => {
                    drop.style.display = 'none';
                });
                lastClickedLink = null;
            };

            document.querySelectorAll('.cat-link').forEach(function (link) {
                const parent = link.closest('.cat-item');
                const dropdown = parent.querySelector('.sub-dropdown');

                if (isTouchDevice) {
                    link.addEventListener('click', function (e) {
                        if (lastClickedLink !== link) {
                            e.preventDefault();
                            hideAllDropdowns();
                            if (dropdown) dropdown.style.display = 'block';

                            lastClickedLink = link;
                            clearTimeout(tapTimeout);
                            tapTimeout = setTimeout(() => {
                                lastClickedLink = null;
                            }, 1000);
                        }
                    });
                } else {
                    parent.addEventListener('mouseenter', () => {
                        hideAllDropdowns();
                        if (dropdown) dropdown.style.display = 'block';
                    });
                    parent.addEventListener('mouseleave', () => {
                        if (dropdown) dropdown.style.display = 'none';
                    });
                }
            });

            document.addEventListener('click', function (e) {
                const isClickInsideCategory = e.target.closest('.cat-item');
                if (!isClickInsideCategory) {
                    hideAllDropdowns();
                }
            });
        });



    </script>