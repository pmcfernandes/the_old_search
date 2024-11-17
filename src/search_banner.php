<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $q = $_POST['q'];
    } else {
        $q = urlencode($_GET['q']);
    }
?>

<table cellpadding="2" cellspacing="2" border="0" width="100%">
    <tr>
        <td rowspan="2" width="120px">
            <a href="index.php">
                <img src="img/computer_logo.jpg" alt="Image logo" width="100px">
            </a>
        </td>
        <td><h1 style="margin:0">The Old Boys | The Search Engine for Vintage Computers</h1></td>
    </tr>
    <tr>
        <td>
            <form action="search.php?ui=1" method="POST">
                <label for="search">Search something with text</label><br>
                <input type="text" id="search" name="q" style="width:400px" accesskey="a" value="<?php echo $q ?>">
                <input type="submit" value="Search now" accesskey="s">
            </form>
        </td>
    </tr>
</table>
<hr size="2">
