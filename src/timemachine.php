<?php

require ('../vendor/autoload.php');
use foroco\BrowserDetection;

$useragent = $_SERVER['HTTP_USER_AGENT'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>The Old Boys | The Search Engine for Vintage Computers</title>
    <style type="text/css">
        body {
            background-color: #D7CCB8;
        }

        label {
            margin-bottom: 15px;
        }

        .page {
            width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .form {
            width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page">
    <p class="center">
        <a href="index.php">
            <img src="img/computer_logo.jpg" alt="Image logo">
        </a>
    </p>
    <h1 class="center">The Old Boys!</h1>
    <h2 class="center">The Time machine, back to glorious days</h2>

    <div class="form">
        <form action="snap.php?ui=1" method="POST">
            <label for="search">Insert a domain name</label><br>
            <input type="text" id="search" name="q" style="width:69%" accesskey="a" value="sapo.pt">
            <select id="year" name="year">
                <?php
                    for ($i = 0; $i <= 15; $i++) {
                        $d = 2010 - $i;
                        echo "<option value='$d'>$d</option>";
                    }
                ?>
            </select>
            <input type="submit" value="Go" accesskey="s">
            <input type="reset" value="Clear" accesskey="c">
        </form>
    </div>
</div>

<p class="center"><a href="index.php">Search engine</a> | <a href="about.php">About</a> </p>

<p>&nbsp;</p>
<p class="center">
    <?php

    $browser = new BrowserDetection();
    $result = $browser->getAll($useragent);

    echo sprintf('You are using %s %s (%s)', $result['browser_name'], $result['browser_version'], $result['os_name']);

    ?>
</p>
<p class="center"><small>Powered by <a href="https://www.duckduckgo.com" target="_blank">DuckDuckGo</a></small></p>
</body>
</html>
