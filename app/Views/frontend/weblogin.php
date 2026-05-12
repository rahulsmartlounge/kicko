<div class="row">
    <div class="text-center logo">
        <a href="">
            <img class="img-align" src="<?php echo base_url() . ASSET_PATH; ?>assets/images/logo.jpg" />
        </a>
    </div>
    <h5 class="text-center" style="margin-top: 5px;">Sign In</h5>

    <!-- <h5 class="text-center">Login</h5> -->
</div>
<div id="loginError" class="text-danger text-center" style="padding:6px;"></div>
<form id="loginForm" method="post">
    <div class="floating-label-group">
        <input type="email" class="form-control" id="email" name="cust_Email" placeholder=" " required />
        <label for="email">Enter the email address</label>
        <div id="emailError" class="text-danger small mt-1" style="display:none;"></div>
    </div>
    <div class="floating-label-group password-wrapper">
        <div class="password-input-wrapper">
            <input type="password" class="form-control" id="password" name="cust_Password" placeholder=" " required />
            <label for="password">Enter your password</label>
            <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
        </div>
    </div>
    <!-- captcha -->
    <!-- <div class="g-recaptcha" data-sitekey="6Le-VXcrAAAAAFdEqJLtM5DxM6GoGl7cJdV6hknL"></div> -->

    <div class="d-flex justify-content-between align-items-center w-100 my-3">
        <div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </div>
        <div>
            <div id="g_id_onload"
                data-client_id="89279377857-k55fvvqvtbk9nib9mc04jfsdgb9k00gn.apps.googleusercontent.com"
                data-context="signin" data-login_uri="https://v4cstaging.co.in/zakhidesigns/google-login-callback"
                data-auto_prompt="false">
            </div>

            <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline"
                data-text="signin_with" data-size="large" data-logo_alignment="left">
            </div>
        </div>
    </div>




    <div style="padding-bottom:8px;">
        <div class="d-flex justify-content-end" style="margin-bottom: 2px;">
            <a id="showForgotForm" class="forgot-style text-decoration-none" style="font-size: 14px; margin-top: 10px; "
                style="text-decoration:none">Forgot Password?</a>
        </div>
        <div class="d-flex justify-content-end align-items-center" style="gap: 5px;">
            <p class="mb-0" style="font-size: 14px;">Don't have an account?</p>
            <a href="#" class="text-decoration-none" id="showRegisterFromLogin" style="font-size: 14px;">Register</a>
        </div>
        <div class="text-center mt-2 px-3">
            <small style="font-size: 13px; color: #7d7d6cff;">

                By continuing, you agree to ZakhiDesigns
                <a href="<?= base_url('Termsandconditions'); ?>" class="text-decoration-none">Terms & Conditions</a> of

                Use and
                <a href="<?= base_url('Privacypolicy'); ?>" class="text-decoration-none">Privacy Policy</a> Notice.
            </small>
        </div>
    </div>
    </div>
</form>
<script>

    $(document).ready(function () {
        $('#togglePassword').on('click', function () {
            const passwordField = $('#password');
            const icon = $(this);

            // Toggle input type
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);

            // Toggle icon class
            icon.toggleClass('bi-eye bi-eye-slash');
        });
    });

    function onSignIn(googleUser) {
        var profile = googleUser.getBasicProfile();
        console.log("ID: " + profile.getId());
        console.log("Name: " + profile.getName());
        console.log("Email: " + profile.getEmail());

        // Optional: Send this data to server via AJAX
    }
    
document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('email');
    const errorDiv = document.getElementById('emailError');
 
    emailInput.addEventListener('blur', function () {
        const email = emailInput.value.trim().toLowerCase();
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
        emailInput.classList.remove('is-invalid');
 
        // Basic format check
        const basicPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!basicPattern.test(email)) {
            errorDiv.textContent = "Please enter a valid email address.";
            errorDiv.style.display = 'block';
            emailInput.classList.add('is-invalid');
            return;
        }
 
        // List of common typo domains with suggestions
        const typoDomains = {
            "gm.com": "gmail.com",
            "gamil.com": "gmail.com",
            "gmial.com": "gmail.com",
            "gmaill.com": "gmail.com",
            "gmail.co": "gmail.com",
            "gmail.con": "gmail.com",
            "gmail.cm": "gmail.com",
            "yaho.com": "yahoo.com",
            "yhoo.com": "yahoo.com",
            "hotmial.com": "hotmail.com",
            "hotmil.com": "hotmail.com",
            "outlok.com": "outlook.com"
        };
 
        const domain = email.split('@')[1];
        if (typoDomains[domain]) {
            errorDiv.innerHTML = `It looks like you typed <strong>${domain}</strong>. Did you mean <strong>${typoDomains[domain]}</strong>?`;
            errorDiv.style.display = 'block';
            emailInput.classList.add('is-invalid');
        }
    });
});
</script>
