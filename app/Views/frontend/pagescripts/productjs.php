<script>
    var baseUrl = "<?= base_url() ?>";
        $(document).ready(function () {
        $("#top-prod-owl-one").owlCarousel({
            items: 4,
            margin: 10,
            nav: true,       // Show next/prev buttons
            dots: false,     // Hide dots
            loop: true,      // Loop the items
            autoplay: false, // Disable automatic slide
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                1000: { items: 4 }
            }
        });
    });
     document.addEventListener('DOMContentLoaded', function () {
        const stock = <?= isset($product['pr_Stock']) ? (int) $product['pr_Stock'] : 0 ?>;
        const orderBtn = document.getElementById('orderNowBtn');

        if (stock < 1 && orderBtn) {
            orderBtn.disabled = true;
            orderBtn.classList.add('btn-secondary');
            orderBtn.classList.remove('btn-dark');
            orderBtn.innerText = 'Out of Stock';
        }
    });
        $(document).ready(function () {
        $('.thumbs .preview a').on('click', function (e) {
            e.preventDefault();
            let newSrc = $(this).data('full');
            let newTitle = $(this).data('title');
            $('#main-image').attr('src', newSrc);
            $('#main-image-link').attr('href', newSrc).attr('title', newTitle);

            // Optionally, set active class for selected thumbnail
            $('.thumbs .preview a').removeClass('selected');
            $(this).addClass('selected');
        });
    });

    document.querySelectorAll('.thumbs a').forEach(thumb => {
        thumb.addEventListener('click', function (e) {
            e.preventDefault();
            const fullImageUrl = this.getAttribute('data-full');
            const mainImage = document.getElementById('main-image');
            const mainImageLink = document.getElementById('main-image-link');

            mainImage.src = fullImageUrl;
            mainImageLink.href = fullImageUrl;

            document.querySelectorAll('.thumbs a').forEach(a => a.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    $('#size').on('change', function () {
    localStorage.setItem(`product_${currentProductId}_size`, $(this).val());
});

$('#qty').on('change keyup', function () {
    localStorage.setItem(`product_${currentProductId}_qty`, $(this).val());
});
function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return '';
    hex = hex.trim().toLowerCase();
    if (/^#([0-9a-f]{3})$/.test(hex)) {
        return '#' + hex[1] + hex[1] + hex[2] + hex[2] + hex[3] + hex[3];
    }
    return hex.startsWith('#') ? hex : `#${hex}`;
}

function rgb2hex(rgb) {
    if (!rgb || rgb.indexOf('#') === 0) return normalizeHex(rgb || '');
    const m = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
    if (!m) return '';
    return "#" +
        ("0" + parseInt(m[1], 10).toString(16)).slice(-2) +
        ("0" + parseInt(m[2], 10).toString(16)).slice(-2) +
        ("0" + parseInt(m[3], 10).toString(16)).slice(-2);
}


function selectColor(color, element) {
    color = normalizeHex(color);
    $('#selected_color').val(color);
    $('.cpicker').removeClass('selected').css('border', '1px solid gray');
    $(element).addClass('selected').css('border', '3px solid gray');
    sessionStorage.setItem('selectedColor', color);
}

function highlightSelectedColor(savedColor) {
    if (!savedColor) return;
    savedColor = normalizeHex(savedColor);

    $('.cpicker').removeClass('selected').css('border', '1px solid gray');

    $('.cpicker').each(function () {
        let dataColor = normalizeHex($(this).data('color'));
        let bgColor = normalizeHex(rgb2hex($(this).css('background-color')));

        if (savedColor === dataColor || savedColor === bgColor) {
            $(this).addClass('selected').css('border', '3px solid gray');
        }
    });
}

function restoreSelectionsFromStorage() {
    const sizeFromStorage  = sessionStorage.getItem('selectedSize');
    const colorFromStorage = sessionStorage.getItem('selectedColor');
    const qtyFromStorage   = sessionStorage.getItem('selectedQty');

    if (sizeFromStorage) $('#size').val(sizeFromStorage);
    if (qtyFromStorage)  $('#qty').val(qtyFromStorage);
    if (colorFromStorage) {
        $('#selected_color').val(colorFromStorage);
        highlightSelectedColor(colorFromStorage);
    }

    let tempOrder = sessionStorage.getItem('tempOrder');
    if (tempOrder) {
        try {
            tempOrder = JSON.parse(tempOrder);

            if (tempOrder.size) {
                $('#size').val(tempOrder.size);
                sessionStorage.setItem('selectedSize', tempOrder.size);
            }

            if (tempOrder.qty) {
                $('#qty').val(tempOrder.qty);
                sessionStorage.setItem('selectedQty', tempOrder.qty);
            }

            if (tempOrder.color) {
                $('#selected_color').val(tempOrder.color);
                highlightSelectedColor(tempOrder.color);
                sessionStorage.setItem('selectedColor', tempOrder.color);
            }
        } catch (e) {
            console.warn("Invalid tempOrder data", e);
        }

        sessionStorage.removeItem('tempOrder');
    }

    // silently remove orderSuccess without showing any message
    sessionStorage.removeItem('orderSuccess');
}

// Trigger restore on load and back navigation
$(document).ready(restoreSelectionsFromStorage);
window.addEventListener('pageshow', function (event) {
    if (event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward") {
        restoreSelectionsFromStorage();
    }
});


$('#orderNowBtn').click(function (e) {
    e.preventDefault();
    $('#orderNowBtn').prop('disabled', true);

    // Always store selection immediately
    const size = $('#size').val();
    const color = $('#selected_color').val();
    const qty = $('#qty').val();
    sessionStorage.setItem('tempOrder', JSON.stringify({ size, color, qty }));

    // 1. Check session live from hidden element
    const isLoggedIn = $('#sessionStatus').data('logged-in') == '1';

    // 2. If not logged in, show login modal
    if (!isLoggedIn) {
        const loginModal = new bootstrap.Modal(document.getElementById('exampleModal'));
        loginModal.show();
        $('#orderNowBtn').prop('disabled', false);
        return;
    }

    // 3. Validate selections
    if (!size || !color || !qty) {
        $('#messageBox')
            .removeClass('alert-success')
            .addClass('alert alert-danger')
            .text('Please select Size, Color and Quantity.')
            .fadeIn();

        $('html, body').animate({ scrollTop: 0 }, 'fast');
        $('#orderNowBtn').prop('disabled', false);

        setTimeout(() => {
            $('#messageBox').fadeOut();
        }, 3000);
        return;
    }

    // 4. Submit via AJAX
    const url = baseUrl + "product/submit";

    $.post(url, $('#orderNowForm').serialize(), function (response) {
        $('#messageBox').removeClass('alert-danger alert-success').hide();

        if (response.status == 1) {
            sessionStorage.removeItem('tempOrder');
            sessionStorage.setItem('orderSuccess', '1');

            $('html, body').animate({ scrollTop: 0 }, 'fast', function () {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    $('#orderNowBtn').prop('disabled', false);
                }
            });
        } else {
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            $('#messageBox')
                .addClass('alert alert-danger')
                .text(response.msg || 'Please select Size, Color and Quantity.')
                .fadeIn();
            $('#orderNowBtn').prop('disabled', false);

            setTimeout(() => {
                $('#messageBox').fadeOut();
            }, 5000);
        }

    }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
        $('#orderNowBtn').prop('disabled', false);

        try {
            const response = JSON.parse(jqXHR.responseText);
            if (response.status === 'unauthorized') {
                const loginModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                loginModal.show();
                return;
            }
        } catch (err) {}

        $('#messageBox')
            .removeClass('alert-success')
            .addClass('alert alert-danger')
            .text('A server error occurred: ' + errorThrown)
            .fadeIn();
    });
});

        document.addEventListener('DOMContentLoaded', function () {
        const preview = document.getElementById('main-preview');

        document.querySelectorAll('.thumb-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const type = this.dataset.type;
                const src = this.dataset.src;
                if (type === 'image') {
                    preview.innerHTML = `<img src="${src}" />`;
                } else if (type === 'video') {
                    preview.innerHTML = `<video src="${src}" controls autoplay></video>`;
                }
            });
        });
    });

    function searchProduct() {
        const keyword = document.getElementById('search').value.trim();
        if (keyword !== '') {
            window.location.href = "<?= base_url('product/search') ?>?keyword=" + encodeURIComponent(keyword);
        }
    }
    
    let loading = false;
    let page = 2;
    let keyword = "<?= esc($keyword ?? '') ?>";
    let lastScrollTop = 0;

    $(document).ready(function () {
        const $loadMore = $('#load-more');
        const $noMoreMsg = $('#no-more-products');
        const $productList = $('#product-list');

        // Scroll Event
        $(window).on('scroll', function () {
            const scrollTop = $(this).scrollTop();
            const windowHeight = $(this).height();
            const documentHeight = $(document).height();

            // Load next batch if near bottom
            if (!loading && scrollTop + windowHeight >= documentHeight - 200) {
                loadNextBatch();
            }

            // Scroll up — remove previous batch to save memory
            if (scrollTop < lastScrollTop && page > 2) {
                const prevBatch = page - 1;
                const $lastBatch = $(`.product-batch[data-batch="${prevBatch}"]`);
                if ($lastBatch.length && scrollTop < $lastBatch.offset().top) {
                    $lastBatch.remove();
                    page--;
                    $loadMore.fadeIn().html('<i class="bi bi-arrow-down-circle" style="font-size: 1.4rem;"></i>');
                    $noMoreMsg.addClass('d-none');
                }
            }

            lastScrollTop = scrollTop;
        });

        function loadNextBatch() {
            loading = true;
            $loadMore.html('<span class="spinner-border spinner-border-sm"></span>');

            const ajaxURL = keyword !== ''
                ? "<?= base_url('product/loadMoreSearch') ?>"
                : "<?= base_url('product/loadMoreByDate') ?>";

            const requestData = keyword !== ''
                ? { keyword: keyword, page: page }
                : { page: page };

            $.ajax({
                url: ajaxURL,
                type: "GET",
                data: requestData,
                success: function (html) {
                    if ($.trim(html) === '') {
                        $loadMore.hide();
                        $noMoreMsg.removeClass('d-none').text('No more products to show.');
                    } else {
                        $productList.append(`<div class="product-batch" data-batch="${page}">${html}</div>`);
                        $loadMore.html('<i class="bi bi-arrow-down-circle" style="font-size: 1.4rem;"></i>').fadeIn();
                        page++;
                    }
                    loading = false;
                },
                error: function () {
                    alert("Failed to load more products.");
                    $loadMore.html('<i class="bi bi-arrow-down-circle" style="font-size: 1.4rem;"></i>').fadeIn();
                    loading = false;
                }
            });
        }
    });
     var swiper = new Swiper(".mySwiper", {
        loop: true,
        spaceBetween: 10,
        slidesPerView: 1, // On mobile
        breakpoints: {
            576: {
                slidesPerView: 2,
                spaceBetween: 10,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 15,
            },
            992: {
                slidesPerView: 4,
                spaceBetween: 20,
            }
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

      document.addEventListener('DOMContentLoaded', function () {
        const toggleIcon = document.getElementById('toggleReviewIcon');
        const reviewContainer = document.querySelector('.order-box');

        let extraReviewWrapper = null;

        if (!toggleIcon || !reviewContainer) return;

        toggleIcon.addEventListener('click', function () {
            
            const productId = this.getAttribute('data-product-id');
            let offset = parseInt(this.getAttribute('data-offset'), 10) || 0;
            const isExpanded = this.getAttribute('data-expanded') === 'true';

            if (!isExpanded) {
              //  Load more reviews
                fetch(`${base_url}product/load-more-reviews/${productId}?offset=${offset}`)
                    .then(response => response.text())
                    .then(data => {
                        if (data.trim() !== '') {
                            extraReviewWrapper = document.createElement('div');
                            extraReviewWrapper.id = 'extra-review-wrapper';
                            extraReviewWrapper.innerHTML = data;

                            // Insert before icon so icon remains at the bottom
                            reviewContainer.insertBefore(extraReviewWrapper, toggleIcon.parentElement);

                            // Scroll into view of new content
                            extraReviewWrapper.scrollIntoView({ behavior: 'smooth' });

                            // Update state
                            toggleIcon.setAttribute('data-expanded', 'true');

                            //toggleIcon.setAttribute('data-offset', offset + 5);
                            toggleIcon.classList.replace('bi-chevron-double-down', 'bi-chevron-double-up');
                        }
                    });


            } else {
                // Collapse the extra reviews
                if (extraReviewWrapper) {
                    extraReviewWrapper.remove();
                    extraReviewWrapper = null;

                    // Scroll back up to original review section
                    reviewContainer.scrollIntoView({ behavior: 'smooth' });

                    toggleIcon.setAttribute('data-expanded', 'false');
                    toggleIcon.classList.replace('bi-chevron-double-up', 'bi-chevron-double-down');
                }
            }
        });

        // Handle 'Read more / Read less'
        document.body.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('toggle-review')) {
                const btn = e.target;
                const parent = btn.closest('.card-text');
                const shortText = parent.querySelector('.short-text');
                const fullText = parent.querySelector('.full-text');

                const isHidden = fullText.classList.contains('d-none');

                if (isHidden) {
                    shortText.classList.add('d-none');
                    fullText.classList.remove('d-none');
                    btn.textContent = 'Read less';
                } else {
                    shortText.classList.remove('d-none');
                    fullText.classList.add('d-none');
                    btn.textContent = 'Read more';
                }
            }
        });
    });


document.addEventListener('DOMContentLoaded', function () {
    const loadMoreBtn = document.getElementById('load-more');
    const productContainer = document.getElementById('product-container');
    const noMore = document.getElementById('no-more-products');

    if (!loadMoreBtn) return;

    loadMoreBtn.addEventListener('click', function () {
        const page = parseInt(this.getAttribute('data-page'), 10);
        const catId = this.getAttribute('data-cat-id');

        fetch(`${base_url}category/loadMoreSearch?id=${catId}&page=${page}`)
            .then(response => response.text())
            .then(data => {
                if (data.trim() === '') {
                    noMore.classList.remove('d-none');
                    loadMoreBtn.style.display = 'none';
                } else {
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = data;
                    wrapper.querySelectorAll('.col-md-3').forEach(card => {
                        productContainer.appendChild(card);
                    });

                    loadMoreBtn.setAttribute('data-page', page + 1);
                }
            })
            .catch(() => {
                alert('Failed to load more products. Try again.');
            });
    });
});
</script>


