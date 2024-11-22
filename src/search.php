<?php
    require ('../vendor/autoload.php');
    require ('./utils.php');
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
    $dotenv->load();

    $proxy = isset($_ENV['PROXY_SERVER']) ? $_ENV['PROXY_SERVER'] : null;

    if (!isset($_ENV['PROXY_USER'])) {
        $proxy_user = null;
    } else {
        $proxy_user = $_ENV['PROXY_USER'] . ':' . $_ENV['PROXY_PASSWD'];
    }

    $results = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $q = $_POST['q'];
    } else {
        $q = urldecode($_GET['q']);
    }

    if (empty($q)) {
        header('Location: index.php');
        exit;
    }

    $url = 'https://html.duckduckgo.com/html/?kl=us-en&q=' . urlencode($q);
    $ch = curl_init($url);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vqd'])) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'q'     => $q,
            's'     => $_POST['s'],
            'v'     => $_POST['v'],
            'o'     => $_POST['o'],
            'dc'    => $_POST['dc'],
            'api'   => $_POST['api'],
            'vqd'   => $_POST['vqd'],
            'kl'    => $_POST['kl'],
        )));
    }

    curl_setopt($ch, CURLOPT_PROXY,  $proxy);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
    $content = curl_exec($ch);
    curl_close($ch);

    $doc = new DOMDocument();
    $doc->validateOnParse = true;
    $doc->loadHTML($content);

    $finder = new DomXPath($doc);
    $spaner = $finder->query("//*[contains(@class, 'result__body')]");

    if ($spaner->length === 0) {
        $results = '<p>No results found</p>';
    } else {
        $i = 1;

        foreach ($spaner as $result) {
            $link = $result->getElementsByTagName('a')->item(0);
            $excerpt = $result->getElementsByTagName('a')->item(3);

            $href = str_replace('//duckduckgo.com/l/?uddg=', '', $link->getAttribute('href'));
            $href = str_replace('http', '/read.php?banner=1&url=http', $href);
            $results .= sprintf('<h3><a href="%s">%s</a></h3><p>%s</p>', $href, $link->textContent, $excerpt->textContent);

            $i++;
        }
    }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>The Old Boys | Searched for: <?php echo strip_tags($q); ?></title>
        <style type="text/css">
            body {
                background-color: #D7CCB8;
            }

            .page-navi {
                padding-bottom: 15px;
                margin-bottom: 15px;
            }

            .page-navi form {
                float: left;
                margin-right: 10px;
            }

            .page-navi form > input[type=submit] {
                padding: 5px 15px;
            }
        </style>
</head>
<body>
    <?php include('search_banner.php'); ?>
    <h3>Searched for: <?php echo strip_tags($q); ?></h3>

    <div id="results">
        <?php echo $results; ?>
    </div>

    <div class="page-navi">
        <?php paginate_results_from_duckduckgo($finder); ?>
    </div>

</body>
</html>