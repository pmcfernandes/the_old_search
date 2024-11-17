<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Location: /read.php?banner=1&url=' . $_POST['q']);
    }

    $url = $_GET['url'];
?>

<form action="read.php?banner=1&url=<?php echo $url; ?>" method="POST">
    <label for="search">Address</label>
    <input type="text" id="search" name="q" value="<?php echo $url; ?>" style="width:50%" accesskey="a">
    <input type="submit" value="Open" accesskey="o">
    <a href="<?php echo $url; ?>">View Original</a> | <a href="javascript:history.back()" accesskey="u">Back history</a>
</form>
<hr size="2">