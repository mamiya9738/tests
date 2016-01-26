<?php
 
require_once('config.php');
require_once('codebird.php'); 
 
session_start();

if (empty($_SESSION['me'])) {
    header('Location: '.SITE_URL.'login.php');
    exit;
}

//htmls出力
function h($s) {
    if($s){
        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
    }
    return "";
}

function ViewList(){
    $params = array(
    'screen_name' => $_SESSION['me']['tw_screen_name']
    );

	\Codebird\Codebird::setConsumerKey(CONSUMER_KEY, CONSUMER_SECRET);
	$cb = \Codebird\Codebird::getInstance();
	$cb->setToken($_SESSION['me']['tw_access_token'], $_SESSION['me']['tw_access_token_secret']);

	$userShow = $cb->users_show($params);
    $strList="<ui>";

    $strList=$strList."<div onclick=\"obj=document.getElementById('menu').style; obj.display=(obj.display=='none')?'block':'none';\">";
    $strList=$strList."<a style=\"cursor:pointer;\">";
    $strList=$strList."<img alt=\"".$userShow-> screen_name."\" src=\"".$userShow-> profile_image_url_https."\" >";
    $strList=$strList."</a>";
    $strList=$strList."</div>";

	$params = array(
		'user_id' => $_SESSION['me']['tw_user_id']
	);
    $user = $cb->friends_ids($params);

    $strList=$strList."<li>";
    foreach ($user->ids as $id){
         $params = array(
          'user_id' => $id
          );
         $userFriend = $cb->users_show($params);

         $strList=$strList."<div id=\"user_icon\">";
         $strList=$strList."<form action=\"userview.php\"  target=\"_blank\" method=\"POST\">";
         $strList=$strList."<input type=\"image\"  alt=\"".$userFriend-> screen_name."\" src=\"".$userFriend-> profile_image_url_https."\" ";
         $strList=$strList."name=\"".$userFriend-> screen_name."\" ";
         $strList=$strList.">";
         $strList=$strList."</form>";
         $strList=$strList."</div>";
     }
    $strList=$strList."</li>";
    $strList=$strList."</ul>";
    echo $strList;
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial=1.0">
    <title>ツイグラ！！</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.8.1/build/cssreset/cssreset-min.css">
    <link rel="stylesheet" href="mystyle.css">
</head>
<body>
<div id="heder">
    <?php echo h(ViewList()); ?>
</div><!-- /heder -->
<a href="#" onClick="window.close(); return false;">閉じる</a>
</body>
</html>