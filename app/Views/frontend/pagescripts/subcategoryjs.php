<script>
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

let currentPage = 1;
const subcatId = $('#subcategory_id').val(); // Hidden input or JS var

$('#loadMoreBtn').on('click', function () {
    currentPage++;
    $.ajax({
        url: "/subcategory/loadMoreSubcategoryProducts",
        type: "GET",
        data: {
            page: currentPage,
            subcat_id: subcatId
        },
        success: function (data) {
            if ($.trim(data) === '') {
                $('#loadMoreBtn').hide();
            } else {
                $('#productContainer').append(data);
            }
        },
        error: function () {
            alert('Failed to load more products.');
        }
    });
});

</script>