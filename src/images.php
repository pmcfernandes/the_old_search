<?php

    function renderImages($readability) {
        foreach ($readability->getImages() as $img_url) {
            echo "<a href=\"image.php?url=" . $img_url . "\" target=\"_blank\">";
            echo "  <img src=\"imagecompressed.php?palette=256&url=" . $img_url. "\" style=\"width:100px;padding:5px;\" />";
            echo "</a>";
        }
    }

