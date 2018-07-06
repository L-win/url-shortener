<?php

require 'func.php';

$app = new App;
$result = $app->boot();

?>
<head>
    <title>Url shortener</title>
</head>
<body style="margin-left:auto;margin-right:auto;max-width:800px;margin-top:70px;padding:50px;" >
<h3>Url shortener</h3>
<form method='post' style="margin:auto;">
    <input type="text" name="originalUrl" style="width:400px;height:40px;">
    <input type="submit" value="submit" name="submit" style="height:40px;">
</form>
<br>
<br>
<?php 

if ( isset($result) ) {
    echo "Link: <a href=u/".$result['short'].">" . '/u/' .$result['short'] . '</a>';
}

?>
</body>