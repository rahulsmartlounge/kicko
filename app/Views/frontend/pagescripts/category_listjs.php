<script>
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 'auto', // keep 'auto' so card width determines layout
        spaceBetween: 10.5,
        freeMode: true,
        grabCursor: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            0: {
                slidesPerView: 3,
                spaceBetween: 6
            },
            768: {
                slidesPerView: '10',
                spaceBetween: 10
            }
        }
    });



var base_url = '<?= base_url(); ?>';
var loading = false;
var page = 2;
var cat_id = "<?= esc($cat_id ?? '') ?>"; 
var lastScrollTop = 0;

$(document).ready(function () {
  const $loadMore = $('#load-more');
  const $noMoreMsg = $('#no-more-products');
  const $productList = $('#product-container');

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
    if (loading) return;
    loading = true;

    const $btn = $('#load-more');
    let page = $btn.data('page');
    let id = $btn.data('cat-id');

    $.ajax({
        url: base_url + 'category/loadMoreSearch?id=' + id + '&page=' + page,

        method: 'GET',
        success: function (res) {
            if (res.trim()) {
                $('#product-container').append(res);
                $btn.data('page', page + 1);
            } else {
                $btn.hide();
                $('#no-more-products').removeClass('d-none');
            }
            loading = false;
        },
        error: function (err) {
            console.error("Load more error:", err);
            loading = false;
        }
    });
}

    function selectColor(color, element) {
        document.getElementById('selected_color').value = color;
        document.querySelectorAll('.cpicker').forEach(el => el.style.border = 'none');
        element.style.border = '3px solid #000';
    }

    $(document).ready(function () {
        let tempOrder = sessionStorage.getItem('tempOrder');
        if (tempOrder) {
            tempOrder = JSON.parse(tempOrder);

            if (tempOrder.size) {
                $('#size').val(tempOrder.size);
            }

            if (tempOrder.color) {
                $('#selected_color').val(tempOrder.color);

                $('.cpicker').removeClass('selected');
                $('.cpicker').each(function () {
                    if ($(this).css('background-color') === tempOrder.color ||
                        rgb2hex($(this).css('background-color')) === tempOrder.color.toLowerCase()) {
                        $(this).addClass('selected');
                    }
                });
            }

            if (tempOrder.qty) {
                $('#qty').val(tempOrder.qty);
            }

            sessionStorage.removeItem('tempOrder');
        }
    });

    // Helper function to convert rgb() to hex
    function rgb2hex(rgb) {
        if (!rgb.startsWith("rgb")) return rgb;
        rgb = rgb.match(/\d+/g);
        return "#" + rgb.map(x => ('0' + parseInt(x).toString(16)).slice(-2)).join('');
    }
    


});

</script>

