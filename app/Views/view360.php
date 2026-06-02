<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360° View</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #000; }
        #viewer { width: 100vw; height: 100vh; }
    </style>
</head>
<body>
    <div id="viewer"></div>

    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script>
        pannellum.viewer('viewer', {
            type:               'equirectangular',
            panorama:           <?= json_encode($imageUrl) ?>,
            autoLoad:           true,
            autoRotate:         -2,
            showFullscreenCtrl: true,
            showZoomCtrl:       true,
            mouseZoom:          true,
            compass:            false,
            showControls:       true
        });
    </script>
</body>
</html>
