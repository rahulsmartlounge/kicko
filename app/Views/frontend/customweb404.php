<!DOCTYPE html>
<html>

<head>
    <title>404 - Page Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }

        h1 {
            font-size: 48px;
            color: #ff0000;
        }

        p {
            font-size: 18px;
        }
    </style>
    <link rel="icon" href="<?php echo base_url() . ASSET_PATH; ?>assets/images/logo.jpg">
</head>

<body>
    <div class="logo">
        <a href="<?= base_url(); ?>">
            <img src="<?= base_url() . ASSET_PATH; ?>assets/images/logo.jpg" alt="Logo" />
        </a>
    </div>
    <h1>404</h1>
    <p>Sorry, the page you are looking for could not be found.</p>
    <a href="<?= base_url(); ?>">Return to Home</a>
</body>

</html>