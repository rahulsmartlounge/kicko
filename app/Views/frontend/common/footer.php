<footer>

    <script>
        var base_url = "<?= base_url(); ?>";
        function handleGoogleCredentialResponse(response) {
            const token = response.credential;
            const payload = JSON.parse(atob(token.split('.')[1]));
            const email = payload.email;

            $.ajax({
                url: base_url + 'GoogleLoginCallback/googleLogin',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    email: email,
                    google_token: token
                }),
                success: function (res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function () {
                    alert('Google login failed.');
                }
            });
        }
    </script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <?php $session = session(); ?>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">

        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <!-- Modal Header: Logo + Close Icon -->
                <div class="modal-header justify-content-center position-relative border-0 pb-0">
                    <!-- Logo centered -->
                    <a href="<?= base_url(); ?>" class="mx-auto">
                        <img src="<?= base_url() . ASSET_PATH ?>assets/images/logo.jpg" alt="Zakhi Logo"
                            style="height: 40px;">
                    </a>

                    <!-- Close icon absolutely positioned to the top-right -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body: Login Form -->
                <div class="modal-body">
                    <div id="loginFormContainer">
                        <h5 class="text-center mb-3">Sign In</h5>

                        <div id="loginError" class="text-danger text-center" style="padding:6px;"></div>

                        <form id="loginForm" method="post" autocomplete="off">
                            <div class="floating-label-group mb-3">
                                <input type="email" class="form-control" id="login_email" name="login_mail" required />

                                <label for="email">Enter the email address</label>
                                <div id="emailError" class="text-danger small mt-1" style="display:none;"></div>
                            </div>

                            <div class="floating-label-group password-wrapper mb-3">
                                <div class="password-input-wrapper position-relative">
                                    <input type="password" class="form-control" id="login_password"
                                        name="login_password" required />
                                    <label for="password">Enter your password</label>
                                    <i class="bi bi-eye-slash toggle-password position-absolute top-50 end-0 translate-middle-y pe-3"
                                        id="togglePassword" style="cursor:pointer;"></i>
                                </div>
                            </div>
                            <div class="g-recaptcha" data-sitekey="6Le-VXcrAAAAAFdEqJLtM5DxM6GoGl7cJdV6hknL"></div>
                            <div>&nbsp;</div>
                            <div>
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </div><!-- Google Sign-In -->
                            <div>&nbsp;</div>
                            <div id="g_id_onload"
                                data-client_id="89279377857-k55fvvqvtbk9nib9mc04jfsdgb9k00gn.apps.googleusercontent.com"
                                data-callback="handleGoogleCredentialResponse" data-auto_prompt="false">
                            </div>

                            <!-- Google Sign-In Button -->
                            <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline"
                                data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left">
                            </div>

                            <div class="d-flex justify-content-end mb-2">
                                <a id="showForgotForm" class="forgot-style text-decoration-none"
                                    style="font-size: 14px;">Forgot Password?</a>
                            </div>

                            <div class="d-flex justify-content-end align-items-center gap-2 mb-2">
                                <p class="mb-0" style="font-size: 14px;">Don't have an account?</p>
                                <a href="#" class="text-decoration-none" id="showRegisterFromLogin"
                                    style="font-size: 14px;">Register</a>
                            </div>
                        </form>
                    </div>
                    <div id="registerFormContainer" style="display: none;">
                        <h5 class="text-center" style="margin-top: 5px;">Your Fashion Journey Starts Here </h5>

                        <div id="regError" class="text-danger text-center p-2" style="color:red;"></div>
                        <form id="registerForm" method="post" autocomplete="off">
                            <div class="floating-label-group">
                                <input type="text" class="form-control" id="custname" name="custname" placeholder=""
                                    required oninput="truncateName(this.value)" />
                                <label for="custname">Name</label>
                            </div>

                            <div class="floating-label-group">
                                <input type="email" class="form-control" id="custemail" name="custemail" placeholder=""
                                    required />
                                <label for="useremail">Email</label>
                            </div>



                            <div class="floating-label-group password-wrapper">
                                <div class="password-input-wrapper">
                                    <input type="password" class="form-control" id="userpassword" name="userpassword"
                                        placeholder=" " required maxlength="15" />
                                    <label for="password">Password</label>
                                    <i class="bi bi-eye-slash toggle-password" id="toggleCurrentPassword"
                                        style="cursor: pointer;"></i>
                                </div>
                                <small class="form-text text-danger">
                                    Password must include at least one uppercase letter, one number, and one special
                                    character.
                                </small>
                            </div>

                            <div class="progress mt-2" id="password-strength-bar" style="height: 8px; display: none;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;"
                                    id="password-strength-fill"></div>
                            </div>
                            <small id="password-strength-text" class="fw-bold"></small>
                            <div>&nbsp;</div>

                            <div class="floating-label-group password-wrapper">
                                <div class="password-input-wrapper">
                                    <input type="password" class="form-control" id="cpassword" name="cust_cpassword"
                                        max-length="15" placeholder=" " required />
                                    <label for="cpassword">Confirm Password</label>
                                    <i class="bi bi-eye-slash toggle-password" id="toggleConfirmPassword"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Create Now</button>
                            </div>
                            <div class="d-flex mt-2">
                                <p class="mb-0">Already have an account with us?</p>
                                <a href="#" class="ms-2 text-decoration-none" id="showLoginFromRegister"
                                    style="text-decoration:none">Login</a>
                            </div>
                        </form>
                    </div>
                    <div id="forgotFormContainer" style="display: none;">
                        <h5 class="text-center" style="margin-top: 7px;">Forgot Password</h5>


                        <form id="forgotEmailForm" method="post" autocomplete="off">
                            <div class="alert p-2" id="messageBox" style="display: none;"></div>

                            <p style="text-align:center;">Enter your email address and we'll send you a link to reset
                                your password.</p>
                            <div class="floating-label-group">
                                <input type="email" class="form-control" id="forgotCustEmail" name="forgotCustEmail"
                                    placeholder=" " required />
                                <label for="email">Email</label>
                            </div>
                            <div class="d-flex mt-2 justify-content-center">
                                <button type="button" class="btn btn-primary" id="forgotEmailSending">
                                    Reset Password
                                </button>
                            </div>
                            <div class="text-end">
                                <a href="" class="ms-2" id="showLoginFromFrgt" style="text-decoration:none">Back to
                                    Login</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Footer: Terms & Privacy -->
                <div class="modal-footer text-center flex-column">
                    <small style="font-size: 13px; color: #7d7d6cff;">
                        By continuing, you agree to ZakhiDesigns
                        <a href="<?= base_url('Termsandconditions'); ?>" class="text-decoration-none">Terms &
                            Conditions</a> of Use and
                        <a href="<?= base_url('Privacypolicy'); ?>" class="text-decoration-none">Privacy Policy</a>
                        Notice.
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="container-lg">
        <div class="row">
            <div class="col-md-3">
                <h4>Your Account<h4>
                        <ul>
                            <?php if (!empty($session->get('zd_uid'))): ?>
                                <li><i class="bi bi-arrow-right"></i>
                                    <a href="<?= base_url('profile#profile'); ?>" class="foot-link login-check">Profile</a>
                                </li>
                            <?php else: ?>
                                <li><i class="bi bi-arrow-right"></i>
                                    <a href="javascript:void(0);" onclick="$('#exampleModal').modal('show');"
                                        class="foot-link login-check">Profile</a>
                                </li>
                            <?php endif; ?>



                            <?php $session = session(); ?>

                            <?php if (!empty($session->get('zd_uid'))): ?>
                                <li><i class="bi bi-arrow-right"></i>
                                    <a href="<?= base_url('profile#orders'); ?>" class="foot-link login-check">My Orders</a>
                                </li>
                            <?php else: ?>
                                <li><i class="bi bi-arrow-right"></i>
                                    <a href="javascript:void(0);" onclick="$('#exampleModal').modal('show');"
                                        class="foot-link login-check">My Orders</a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($session->get('zd_uid'))): ?>
                                <?php if (!empty($pr_Id)): ?>
                                    <li><i class="bi bi-arrow-right"></i>
                                        <a href="<?= base_url('profile?pr_Id=' . $pr_Id . '#address'); ?>"
                                            class="foot-link login-check">Address</a>
                                    </li>
                                <?php else: ?>
                                    <li><i class="bi bi-arrow-right"></i>
                                       <a href="<?= base_url('profile#address'); ?>" class="foot-link login-check">Address</a>

                                    </li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li><i class="bi bi-arrow-right"></i>
                                    <a href="javascript:void(0);" onclick="$('#exampleModal').modal('show');"
                                        class="foot-link login-check">Address</a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($session->get('zd_uid'))): ?>
                                <li><i class="bi bi-arrow-right"></i>
                                    <a href="<?= base_url('profile#orders'); ?>" class="foot-link login-check">Track
                                        Orders</a>
                                </li>
                            <?php else: ?>
                                <li><i class="bi bi-arrow-right"></i>
                                    <a href="javascript:void(0);" onclick="$('#exampleModal').modal('show');"
                                        class="foot-link login-check">Track Orders</a>
                                </li>
                            <?php endif; ?>

                        </ul>

            </div>
            <div class="col-md-3">
                <h4>Products<h4>
                        <ul>
                            <li><i class="bi bi-arrow-right"></i><a class="foot-link"
                                    href="<?= base_url('product/viewcollection'); ?>">Price Drop</li>
                            <li><i class="bi bi-arrow-right"></i><a class="foot-link"
                                    href="<?= base_url('product/viewcollection'); ?>">Products</a></li>
                            <li><i class="bi bi-arrow-right"></i><a class="foot-link"
                                    href="<?= base_url('/#top-products'); ?>">Best Sellers</a></li>
                            <li><i class="bi bi-arrow-right"></i><a class="foot-link"
                                    href="<?= base_url(); ?>">Sitemap</a></li>
                        </ul>
            </div>
            <div class="col-md-3">
                <h4>Our Company<h4>
                        <ul>
                            <li><i class="bi bi-arrow-right"></i> <a class="foot-link"
                                    href="<?= base_url('delivery'); ?>">Delivery</a></li>
                            <li><i class="bi bi-arrow-right"></i> <a class="foot-link"
                                    href="<?= base_url('Privacypolicy'); ?>">Privacy Policy</a></li>
                            <li><i class="bi bi-arrow-right"></i> <a class="foot-link"
                                    href="<?= base_url('Termsandconditions'); ?>">Terms & Conditions</a></li>
                            <li><i class="bi bi-arrow-right"></i> <a class="foot-link"
                                    href="<?= base_url('Return_refundpolicy'); ?>">Return and Refund Policy</a></li>
                        </ul>
            </div>
            <?= view_cell('App\Cells\FooterCell::storeInfo') ?>

        </div>
        <div class="row">
            <div class="col-md-12 text-center social-ico">
                <i class="bi bi-facebook"></i>
                <i class="bi bi-twitter"></i>
                <i class="bi bi-instagram"></i>
                <i class="bi bi-youtube"></i>
            </div>
        </div>
    </div>
</footer>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- intlTelInput CSS -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18/build/js/intlTelInput.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<!-- intlTelInput JS -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script> -->
<!-- Utility script for validation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>


<!-- <script src="<?php echo base_url() . ASSET_PATH; ?>assets/js/jquery-3.7.1.min.js"></script> -->
<!-- <script src="<?php echo base_url() . ASSET_PATH; ?>assets/vendors/owlcarousel/owl.carousel.js"></script> -->
<!-- <script src="<?php echo base_url() . ASSET_PATH; ?>assets/js/bootstrap.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>


<script>
    function resetPasswordStrength() {
        $('#password-strength-bar').hide();
        $('#password-strength-fill').css('width', '0%');
        $('#password-strength-text').text('');
    }
    $(document).ready(function () {
        var $form = $('#forgotEmailForm');
        var $btn = $('#forgotEmailSending');
        var $box = $('#messageBox');

        // Prevent form submission on enter
        $form.on('submit', function (e) {
            e.preventDefault();
            $btn.trigger('click'); // Trigger the AJAX logic
        });
        $btn.on('click', function () {
            var link = "<?= base_url('weblogin/webForgotEmailSend'); ?>";
            // var $btn = $(this);
            var originalText = $btn.html();

            // Set button to loading state
            $btn
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');

            $.post(link, $('#forgotEmailForm').serialize(), function (response) {
                let $box = $('#messageBox');

                if (response.status == 1) {
                    $box.removeClass('alert-danger').addClass('alert-success').text(response.msg).show();
                } else if (response.status == 0) {
                    $box.removeClass('alert-success').addClass('alert-danger').text(response.msg).show();
                } else {
                    $box.removeClass('alert-success').addClass('alert-danger').text("Invalid Email Format.").show();
                }

                setTimeout(() => {
                    $box.fadeOut();
                }, 3000);

            }, 'json')
                .always(function () {
                    // Reset button
                    $btn.prop('disabled', false).html(originalText);
                });
        });
    });


    function openRespMenu() {
        var x = document.getElementById("respTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
    $(document).ready(function () {
        $('.bi-search').click(function () {
            $('.searchbox').find('input').toggle({
                right: '250px'
            });
        });

        var topowl = $('#top-prod-owl,#top-prod-owl-two');
        topowl.owlCarousel({
            margin: 10,
            loop: true,
            nav: true, // Enables navigation
            navText: ["<", ">"], // Custom navigation text/icons

            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 4
                }
            }
        });
    });

    //Login form open

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Skip if href is exactly '#' or empty
            if (href === '#' || href.length < 2) {
                return;
            }

            e.preventDefault();

            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            const tooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
            if (tooltip) {
                tooltip.dispose();
            }
        });
    });

    $(document).ready(function () {


        // Switch to Register form
        $('#showRegisterFromLogin').click(function (e) {
            e.preventDefault();
            $('#loginFormContainer').hide();
            $('#forgotFormContainer').hide();
            $('#registerFormContainer').show();
        });

        // Switch back to Login from Register
        $('#showLoginFromRegister').click(function (e) {
            e.preventDefault();
            $('#registerFormContainer').hide();
            $('#forgotFormContainer').hide();
            $('#loginFormContainer').show();
        });

        // Switch to Forgot form from Login
        $('#showForgotForm').click(function (e) {
            e.preventDefault();
            $('#loginFormContainer').hide();
            $('#registerFormContainer').hide();
            $('#forgotFormContainer').show();
        });

        // Switch back to Login from Forgot
        $('#showLoginFromFrgt').click(function (e) {
            e.preventDefault();
            $('#registerFormContainer').hide();
            $('#forgotFormContainer').hide();
            $('#loginFormContainer').show();
        });

        // ✅ Always reset to Login form when modal opens
        $('#exampleModal').on('show.bs.modal', function () {
            $('#registerFormContainer').hide();
            $('#forgotFormContainer').hide();
            $('#loginFormContainer').show();

            // Optionally clear form inputs
            $('#registerFormContainer input').val('');
            $('#forgotFormContainer input').val('');
            $('#loginFormContainer input').val('');
            $('#messageBox').hide(); // Optional: hide alerts
        });

    });



$(document).on('submit', '#loginForm', function (e) {
    e.preventDefault();

    let email = $('#login_email').val();
    let password = $('#login_password').val();
    let recaptchaResponse = grecaptcha.getResponse(); // Get reCAPTCHA token

    if (!recaptchaResponse) {
        $('#loginError').text('Please complete the reCAPTCHA.').show();

        setTimeout(function () {
            $('#loginError').fadeOut();
        }, 3000);
        return;
    }

    $.ajax({
        url: '<?= base_url('customerauth'); ?>',
        type: 'POST',
        data: {
            login_email: email,
            login_password: password,
            'g-recaptcha-response': recaptchaResponse
        },
        success: function (res) {
            let data = JSON.parse(res);

            if (data.status == 1) {
                // Successful login — reload page
                window.location.reload();
            } else {
                // Show error message and hide after 3 seconds
                $('#loginError').text(data.msg).show();

                setTimeout(function () {
                    $('#loginError').fadeOut();
                }, 3000);

                grecaptcha.reset(); // Reset the reCAPTCHA box
            }
        },
        error: function () {
            $('#loginError').text('Something went wrong. Please try again.').show();

            setTimeout(function () {
                $('#loginError').fadeOut();
            }, 3000);

            grecaptcha.reset(); // Reset on failure too
        }
    });
});

    // Add this FIRST
    function togglePassword(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(toggleId);

        if (!input || !icon) return;

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const toggleLogin = document.getElementById('togglePassword');
        const toggleCurrent = document.getElementById('toggleCurrentPassword');
        const toggleConfirm = document.getElementById('toggleConfirmPassword');

        if (toggleLogin) {
            toggleLogin.addEventListener('click', function () {
                togglePassword('login_password', 'togglePassword');
            });
        }

        if (toggleCurrent) {
            toggleCurrent.addEventListener('click', function () {
                togglePassword('userpassword', 'toggleCurrentPassword');
            });
        }

        if (toggleConfirm) {
            toggleConfirm.addEventListener('click', function () {
                togglePassword('cpassword', 'toggleConfirmPassword');
            });
        }
    });

    let iti = null;

    $(document).ready(function () {

        // Submit Handler
        $(document).on('submit', '#registerForm', function (e) {
            e.preventDefault();
            $('#regError').stop(true, true).hide().removeClass('text-danger text-success').html('');

            const password = $('#userpassword').val().trim();
            const cpassword = $('#cpassword').val().trim();
            const email = $('#custemail').val().trim();
            const name = $('#custname').val().trim();

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('Please enter a valid email address.');
                return;
            }

            if (password !== cpassword) {
                showError('Passwords do not match.');
                return;
            }

            $.ajax({
                url: '<?= base_url('weblogin/create') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.status === 1) {
                        $('#regError').removeClass('text-danger').addClass('text-success').html(response.msg).fadeIn();

                        resetPasswordStrength();
                        $('#registerForm')[0].reset();

                        setTimeout(() => {
                             const exampleModal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
                             if (exampleModal) {
            exampleModal.hide();
        }
                        }, 3000);


                        setTimeout(() => {
                            $('#regError').fadeOut('slow', function () {
                                $(this).removeClass('text-success').html('').show();
                            });
                        }, 3000);
                    } else {
                        showError(response.msg);
                    }
                },
                error: function () {
                    showError('An error occurred. Please try again.');
                }
            });
        });

        function showError(message) {
            $('#regError')
                .removeClass('text-success')
                .addClass('text-danger')
                .html(message)
                .fadeIn();

            setTimeout(() => {
                $('#regError').fadeOut('slow', function () {
                    $(this).removeClass('text-danger').html('').show();
                });
            }, 3000);
        }


    });

</script>
<script>
    const passwordInput = document.getElementById('userpassword');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthFill = document.getElementById('password-strength-fill');
    const strengthText = document.getElementById('password-strength-text');

    $(document).on('input', '#userpassword', function () {
        const value = this.value;
        const result = calculatePasswordStrength(value);

        if (value.length > 0) {
            $('#password-strength-bar').show();
            $('#password-strength-fill')
                .css('width', result.percent + '%')
                .removeClass()
                .addClass('progress-bar bg-' + result.color);

            $('#password-strength-text')
                .text(result.label)
                .css('color', getTextColor(result.color));
        } else {
            $('#password-strength-bar').hide();
            $('#password-strength-fill').css('width', '0%');
            $('#password-strength-text').text('').css('color', '');
        }
    });
    $('#registerModal').on('hidden.bs.modal', function () {
        resetPasswordStrength();
        $('#registerForm')[0].reset(); // Optional: clear form
    });




    function calculatePasswordStrength(password) {
        let score = 0;

        if (password.length >= 8) score++;                  // ✔️ Proper length
        if (/[A-Z]/.test(password)) score++;                // ✔️ Uppercase
        if (/[a-z]/.test(password)) score++;                // ✔️ Lowercase
        if (/\d/.test(password)) score++;                   // ✔️ Number
        if (/[^A-Za-z0-9]/.test(password)) score++;         // ✔️ Special character

        switch (score) {
            case 0:
            case 1:
                return { percent: 20, color: 'danger', label: 'Very Weak' };
            case 2:
                return { percent: 40, color: 'warning', label: 'Weak' };
            case 3:
                return { percent: 60, color: 'info', label: 'Moderate' };
            case 4:
                return { percent: 80, color: 'primary', label: 'Strong' };
            case 5:
                return { percent: 100, color: 'success', label: 'Very Strong' };
            default:
                return { percent: 0, color: 'secondary', label: '' };
        }
    }


    // Match Bootstrap contextual colors to text hex codes
    function getTextColor(color) {
        switch (color) {
            case 'danger': return '#dc3545';   // red
            case 'warning': return '#ffc107';  // yellow
            case 'info': return '#17a2b8';     // light blue
            case 'primary': return '#007bff';  // blue
            case 'success': return '#28a745';  // green
            default: return '#6c757d';         // gray (secondary)
        }
    }


    $('#registerModal').on('shown.bs.modal', function () {

        resetPasswordStrength();
        $('#registerForm')[0].reset();
        const value = passwordInput.value;
        if (value.length > 0) {
            strengthBar.style.display = 'block';
            const result = calculatePasswordStrength(value);

            strengthFill.style.width = result.percent + '%';
            strengthFill.className = 'progress-bar bg-' + result.color;
            strengthText.innerText = result.label;
            strengthText.style.color = getTextColor(result.color);
        } else {
            strengthBar.style.display = 'none';
            strengthText.innerText = '';
            strengthText.style.color = '';
        }
    });
    function truncateName(name) {
        const preview = document.getElementById("namePreview");
        if (!preview) return;

        preview.textContent = name.length > 6 ? name.substring(0, 6) + "..." : name;
    }

    $(document).ready(function() {
        // Disable right-click on all images, including dynamically added ones
        $('body').on('contextmenu', 'img', function(e) {
            return false; // Prevents the default context menu from appearing
        });
    });


    
</script>


</html>