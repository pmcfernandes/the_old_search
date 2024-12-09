<?php
    if (!extension_loaded('gd')) {
        echo 'gd extension is disabled';
        exit;
    }

    $url = $_GET['url'];
    $palette = isset($_GET['palette']) ? $_GET['palette'] : 0;
    $max_width = isset($_GET['max_width']) ? $_GET['max_width'] : 300;
    $max_height = isset($_GET['max_height']) ? $_GET['max_height'] : 300;

    if (empty($url)) {
        exit;
    }

    if (substr($url, 0, 4) !== 'http') {
        exit;
    }

    $imageType = substr(substr($url, strrpos($url, '.')), 1);

    switch ($imageType) {
        case 'jpg':
        case 'jpeg':
            $img = imagecreatefromjpeg($url);
            break;
        case 'png':
            $img = imagecreatefrompng($url);
            break;
        default:
            echo  "3";
            exit;
    }

    $size  = [imagesx($img), imagesy($img)];

    $resampled_width = 0;
    $resampled_height = 0;

    if ($size[0] >= $size[1]) {
        $resampled_width = $max_width;
        $resampled_height = ($size[1] / $size[0]) * $resampled_width;
    } else {
        $resampled_height = $max_height;
        $resampled_width = ($size[0] / $size[1]) * $resampled_height;
    }

    $new_image = imagecreatetruecolor($resampled_width, $resampled_height);

    if (intval($palette) !== 0) {
        imagetruecolortopalette($new_image, false, intval($palette));
    }

    imagecopyresampled($new_image, $img,0,0,0,0, $resampled_width, $resampled_height, $size[0], $size[1]);

    header('Content-type: image/' . $imageType);

    switch ($imageType) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($new_image, NULL, 80);
            break;
        case 'png':
            imagepng($new_image, NULL);
            break;
        default:
            break;
    }

    imagedestroy($img);
    imagedestroy($new_image);
    die();