<?php

    require ('../vendor/autoload.php');
    use foroco\BrowserDetection;

    /**
     * Show browser information in page render workflow
     * @return void
     */
    function show_browser_info() : void {
        $browser = new BrowserDetection();
        $result = $browser->getAll($_SERVER['HTTP_USER_AGENT']);
        echo sprintf('You are using %s %s (%s)', $result['browser_name'], $result['browser_version'], $result['os_name']);
    }

    /**
     * Show pagination in DuckDuckGo results in page render worlflow
     * @param $finder
     * @return void
     */
    function paginate_results_from_duckduckgo($finder) : void {
        $spanner = $finder->query("//*[contains(@class, 'nav-link')]");

        if ($spanner->length > 0) {
            foreach ($spanner as $form) {
                $elements = $form->getElementsByTagName("input");
                echo "<form action='search.php?ui=1' method='POST'>";

                foreach ($elements as $element) {
                    echo '<input type="' . $element->getAttribute('type') . '" name="' . $element->getAttribute('name') . '" value="' . $element->getAttribute('value') . '" />';
                }

                echo "</form>";
            }
        }
    }

    /**
     * Parse file to check if is a download or other html / plain content-type
     * @param $url
     * @return void
     */
    function parse_file_to_download($url) : void {
        $compatible_content_types = [
            "text/html", "text/plain"
        ];

        $url_parsed = parse_url($url);

        // Get headers from url
        $context = stream_context_create(['http' => array('method' => 'HEAD')]);
        $headers = get_headers($url, true, $context);
        $contentType = $headers['Content-Type'];

        if (!$contentType) {
            return;
        }

        if (is_array($headers['Content-Type'])) {
            $contentType = end($contentType);
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
    }