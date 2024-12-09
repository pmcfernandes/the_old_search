<?php
    error_reporting(E_ALL);

    require_once  ('../vendor/autoload.php');
    require ('./utils.php');
    require ('./images.php');

    use fivefilters\Readability\Readability;
    use fivefilters\Readability\Configuration;
    use fivefilters\Readability\ParseException;

    $url = urldecode($_GET['url']) ;
    $banner = isset($_GET['banner']) ? $_GET['banner'] : '0';
    $banner_query = ($banner == '1' ? 'banner=1&' : '');

    $url_parsed = parse_url($url);
    $host = $url_parsed['host'];

    if (empty($url)) {
        echo "Syntax: read.php?url=https://google.com";
        exit;
    }

    // First try to download file
    parse_file_to_download($url);

    // Read document using readability API
    $readability = new Readability(new Configuration([
        'originalURL' => sprintf("http://%s", $host),
        'fixRelativeURLs' => true,
    ]));


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
    $html = curl_exec($ch) ;
    curl_close($ch);

    try {
        $readability->parse($html);
    } catch (ParseException $e) {
        echo sprintf('Error processing text: %s', $e->getMessage());
        die();
    }

    $readable_article = $readability->getContent();
    $readable_article = strip_tags($readable_article, '<a><ol><ul><li><br><p><small><font><b><strong><i><em><blockquote><h1><h2><h3><h4><h5><h6>');
    $readable_article = str_replace('strong>', 'b>', $readable_article);
    $readable_article = str_replace('em>', 'i>', $readable_article);
    $readable_article = str_replace('href="http', 'href="/read.php?' . $banner_query . 'url=http', $readable_article);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo htmlspecialchars($readability->getTitle()); ?></title>
	</head>
	<body>
        <?php
            if ($banner === '1') include ("reader_banner.php");
            renderImages($readability);
            echo $readable_article;
        ?>
</body>
</html>