<?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $q = $_POST['q'];
        $year = $_POST['year'];
        $ui = $_GET['ui'];

        header('Location: /snap.php?ui=' . $ui . '&year=' . $year .'&url=' . $q);
        exit;
    }

    $year = $_GET['year'];
    $url = $_GET['url'];

    if (empty($url) || empty($year)) {
        echo "Syntax: snap.php?&year=1998&url=https://google.com";
        exit;
    }

    $url_parsed = parse_url($url);
    $host = $url_parsed['path']; // extract host

    $wayback_url = 'https://archive.org/wayback/available?url=' . $host . '&timestamp=' . $year . '1201';

    if (!$json_result = file_get_contents($wayback_url)) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

    $wayback_result = json_decode($json_result);

    if ($wayback_result->archived_snapshots->closest) {
        if ($wayback_result->archived_snapshots->closest->available == 'true') {
            $data_url = $wayback_result->archived_snapshots->closest->url;

            $opts = array(
                'http'=>array(
                    'method' => "GET",
                    'header' => "Content-type: text/html; charset=ISO-8859-1\r\n"
                )
            );

            $context = stream_context_create($opts);

            if (!$html = file_get_contents($data_url, false, $context)) {
                header("HTTP/1.1 404 Not Found");
                exit;
            }

            $doc = new DOMDocument();
            $doc->validateOnParse = true;
            $doc->loadHTML($html);

            $doc_title = $doc->getElementsByTagName("title")->item(0)->nodeValue;

            header('Content-Type: text/html; charset=ISO-8859-1');
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");

            $html = htmlspecialchars_decode($html, ENT_QUOTES | ENT_HTML401 | ENT_SUBSTITUTE);
            $html = str_replace('href="/web/', 'href="http://web.archive.org/web/', $html);
            $html = str_replace('src="/web/', 'src="http://web.archive.org/web/', $html);
        }
    } else {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo $doc_title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <style type="text/css">
            #wm-ipp-print {
                display: none;
            }
        </style>
</head>
<body>
    <?php echo $html; ?>
</body>
</html>