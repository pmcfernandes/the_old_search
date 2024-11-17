<?php
    error_reporting(E_ALL);

    require_once  ('../vendor/autoload.php');
    use fivefilters\Readability\Readability;
    use fivefilters\Readability\Configuration;
    use fivefilters\Readability\ParseException;

    $compatible_content_types = [
        "text/html", "text/plain"
    ];

    $url = urldecode($_GET['url']) ;
    $banner = isset($_GET['banner']) ? $_GET['banner'] : '0';
    $banner_query = ($banner == '1' ? 'banner=1&' : '');

    $url_parsed = parse_url($url);
    $host = $url_parsed['host'];

    if (empty($url)) {
        echo "Syntax: read.php?url=https://google.com";
        exit;
    }

    // Get headers from url
    $context = stream_context_create(['http' => array('method' => 'HEAD')]);
    $headers = get_headers($url, true, $context);

    if (is_array($headers['Content-Type'])) {
        $contentType = $headers['Content-Type'][count($headers['Content-Type']) - 1];
    } else {
        $contentType = $headers['Content-Type'];
    }

    // Process downloads as download file
    if (!empty($contentType)) {
        if (!in_array(explode(";", $contentType)[0], $compatible_content_types)) {
            $filesize = $headers['Content-Length'];
            $filename = basename($url_parsed['path']);

            if (!$filename) {
                $filename = "download";
            }

            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . $filesize);
            header('Content-Disposition: attachment; filename="'. $filename . '"');

            readfile($url);
            exit;
        }
    }

    // Read document using readability API
    $readability = new Readability(new Configuration([
        'originalURL' => sprintf("http://%s", $host),
        'fixRelativeURLs' => true,
    ]));

    if (!$html = file_get_contents($url)) {
        echo "Failed to read URL: $url";
        exit;
    }

    try {
        $readability->parse($html);
    } catch (ParseException $e) {
        echo sprintf('Error processing text: %s', $e->getMessage());
        die();
    }

    $readable_article = $readability->getContent();
    $readable_article = str_replace('strong>', 'b>', $readable_article);
    $readable_article = str_replace('em>', 'i>', $readable_article);
    $readable_article = strip_tags($readable_article, '<a><ol><ul><li><br><p><small><font><b><strong><i><em><blockquote><h1><h2><h3><h4><h5><h6>');

    $readable_article = str_replace('href="http', 'href="/read.php?' . $banner_query . 'url=http', $readable_article);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo $readability->getTitle(); ?></title>
	</head>
	<body>
        <?php
            if ($banner === '1') include ("reader_banner.php");
            echo $readable_article;
        ?>
</body>
</html>