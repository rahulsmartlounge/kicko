<script>
document.getElementById("reviewForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch("<?= base_url('review/submit') ?>", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(res => res.json())
    .then(data => {
        const div = document.getElementById("reviewResponse");
        let messageHtml = "";

        // Check if validation errors (object)
        if (typeof data.message === 'object') {
            for (const key in data.message) {
                if (data.message.hasOwnProperty(key)) {
                    messageHtml += `<div class="text-danger">• ${data.message[key]}</div>`;
                }
            }
        } else {
            messageHtml = `<div>${data.message}</div>`;
        }

        div.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${messageHtml}</div>`;

        // On success, reset and reload
       if (data.status === 'success') {
    reviewResponse.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
    form.reset();

    // ✅ Wait 4 seconds before reload so user sees the message
    setTimeout(() => window.location.reload(), 4000);
}

        // Clear message after 3 seconds
        setTimeout(() => {
            div.innerHTML = '';
        }, 3000);
    })
    .catch(error => {
        console.error("Error submitting review:", error);
        document.getElementById("reviewResponse").innerHTML = `<div class="alert alert-danger">An unexpected error occurred.</div>`;

        setTimeout(() => {
            document.getElementById("reviewResponse").innerHTML = '';
        }, 3000);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll('#starRating i');
    const ratingInput = document.getElementById('ratingInput');

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const rating = this.getAttribute('data-value');
            ratingInput.value = rating;

            // Update star visuals
            stars.forEach(s => {
                if (s.getAttribute('data-value') <= rating) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });
    });
});
$(document).on('click', '.read-toggle', function () {
    const $card = $(this).closest('.card-text');
    const $short = $card.find('.short-review');
    const $full = $card.find('.full-review');

    if ($full.hasClass('d-none')) {
        $short.addClass('d-none');
        $full.removeClass('d-none');
        $(this).text('Read Less');
    } else {
        $short.removeClass('d-none');
        $full.addClass('d-none');
        $(this).text('Read More');
    }
});
</script>
