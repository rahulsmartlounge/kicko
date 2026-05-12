<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="<?php echo base_url() . ASSET_PATH; ?>assets/images/logo.jpg">
    <title>Reset Link Expired</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .expired-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="expired-box">
         <div class="row justify-content-center">
                <div class="col-6 logo">
                    <a href="<?= base_url(); ?>">
                        <img src="<?= base_url() . ASSET_PATH; ?>assets/images/logo.jpg" alt="Logo" />
                    </a>
                </div>
            </div>
            <div>&nbsp;</div>
        <h2>Oops! Link Expired</h2>
        <p>Your password reset link has expired. Please request a new one.</p>
        <a href="<?= base_url('/') ?>" class="btn btn-primary">Back to Login</a>
    </div>
</body>
</html>
