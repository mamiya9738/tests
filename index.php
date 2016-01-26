<?php
 
require_once('config.php');
require_once('codebird.php'); 
 
 session_start();
if (empty($_SESSION['me'])) {
    header('Location: '.SITE_URL.'login.php');
    exit;
}

\Codebird\Codebird::setConsumerKey(CONSUMER_KEY, CONSUMER_SECRET);
$cb = \Codebird\Codebird::getInstance();
 
$cb->setToken($_SESSION['me']['tw_access_token'], $_SESSION['me']['tw_access_token_secret']);
$tweets=""; 

    //$tweets = (array) $cb->statuses_homeTimeline(array("count"=>"25", "include_entities" => "false")); 
    //$tweets = (array) $cb->statuses_homeTimeline(array("count"=>"200", "include_entities" => "true", "since_id "=>"366589073734897665")); 
    $tweets = (array) $cb->statuses_homeTimeline(array("count"=>"200", "include_entities" => "true")); 
array_pop($tweets);

//htmls出力
function h($s) {
    if($s){
        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
    }
    return "";
}

//imageサイト判定URL
define ('TWIPIC_URL','http://twitpic.com/');
define ('INSTAG_URL','http://instagram.com/p/');
define ('TWIPPLE_URL','http://p.twipple.jp/');

//imege判定
function isImageURL($url){
    //twipicか?
    if(strstr($url,TWIPIC_URL)
     or strstr($url,INSTAG_URL)
     or strstr($url,TWIPPLE_URL)
     ){
        return true;
    }
    else{
        return false;
    }
}

function getImageURL($url){
    //twipicか?
    if(strstr($url,TWIPIC_URL)){
        return  "<a href=" . str_replace('twitpic.com/','twitpic.com/show/full/',strstr($url,TWIPIC_URL))  . ">".
                      "<img src= " . str_replace('twitpic.com/','twitpic.com/show/thumb/',strstr($url,TWIPIC_URL)) . "></a>";
    }
    else if(strstr($url,INSTAG_URL)){
        return  "<a href=" . $url. "media/?size=l>".
                      "<img src= " . $url. "media/?size=m></a>";
    }
    else if(strstr($url,TWIPPLE_URL)){
        return "<a href=" . str_replace('p.twipple.jp/','p.twpl.jp/show/large/',strstr($url,TWIPPLE_URL))  . ">".
                    "<img src= " . str_replace('p.twipple.jp/','p.twpl.jp/show/thumb/',strstr($url,TWIPPLE_URL)) .  "></a>";
    }
    else{
//        return   "<a href=" . $url . ">".$url."</a>";
        return false;
    }
}

function getRetweetCount($retweet_count){
    if($retweet_count){
          return $retweet_count;
    }
    else{
        return 0;
    }
}

function getFavouritesCount($favourites_count){
    if($favourites_count){
          return $favourites_count;
    }
    else{
        return 0;
    }
}

function getTime($created_at){
    $timestamp = strtotime($created_at);
    $datetime = date('Y-m-d H:i:s', $timestamp);
    return $datetime;
}

function getTextLine($text){
    //＠の処理
    $text = preg_replace("/(?<![0-9a-zA-Z'\"#@=:;])@([0-9a-zA-Z_]{1,15})/u",
                    "@<a href=\"http://twitter.com/\\1\">\\1</a>", $text);
    //#の処理の処理
    $text = preg_replace("/#(w*[一-龠_ぁ-ん_ァ-ヴー]+|[a-zA-Z0-9]+|[a-zA-Z0-9]w*)/u",
                    "<a href=\"http://twitter.com/search?q=%23\\1\">#\\1</a>", $text);

    //URLの処理
    return $text;       
}


function getFriendsIds($userID){
$cb = \Codebird\Codebird::getInstance();
 
//echo $userID;
            $cb->setToken($_SESSION['me']['tw_access_token'], $_SESSION['me']['tw_access_token_secret']);

            $params = array(
                'user_id' => $userID
            );
            $reply = $cb->friends_ids($params);
    return $reply;
}

function getUserShow($params){
$cb = \Codebird\Codebird::getInstance();
 
//echo $rtUser;
            $cb->setToken($_SESSION['me']['tw_access_token'], $_SESSION['me']['tw_access_token_secret']);

            $reply = $cb->users_show($params);
//echo $rtUser;
    return $reply;
}

//項目ごとの一覧
function viewTextList($tweet){
    $strList="<ul>";

//RTユーザ表示
    $rtflg = substr($tweet-> text,0,2);
    if($rtflg==="RT"){
        $strPos = strpos($tweet-> text,":");
        if($strPos !== false)
        {
             $rtUser = substr($tweet-> text,4,$strPos-4);
             $strtext =substr($tweet-> text,$strPos+1);
             $tweet-> text = $strtext;

             $strList=$strList."<div id=\"rt_user_menu\">";
             $strList=$strList."<li>";
             $strList=$strList."<div id=\"rt_user_icon\">";
             $strList=$strList."<form action=\"userview.php\"  target=\"_blank\" method=\"POST\">";
             $strList=$strList."<input type=\"image\" width=\"24\" height=\"24\" alt=\"".$tweet->user-> screen_name."\" src=\"".$tweet->user-> profile_image_url_https."\" ";
             $strList=$strList."name=\"".$tweet->user-> screen_name."\" ";
             $strList=$strList.">";
             $strList=$strList."</form>";
             $strList=$strList."</div>";
             $strList=$strList."<p>";
             $strList=$strList."<a href=\"http://twitter.com/".$tweet->user-> screen_name."\">".$tweet->user->name."</a>";
             $strList=$strList."さんがRTしました";
             $strList=$strList."</p>";
             $params = array(
                'screen_name' => $rtUser
            );
             $userShow = getUserShow($rtUser);
             $tweet->user-> screen_name = $rtUser;
             $tweet->user-> profile_image_url_https = $userShow->profile_image_url_https;
             $tweet->user-> name = $userShow->name;
             $strList=$strList."</li>";
             $strList=$strList."</div>";

        }
    }

    $strList=$strList."<div id=\"user_menu\">";
    $strList=$strList."<li>";
    $strList=$strList."<div id=\"user_icon\">";
    $strList=$strList."<form action=\"userview.php\"  target=\"_blank\" method=\"POST\">";
    $strList=$strList."<input type=\"image\"  alt=\"".$tweet->user-> screen_name."\" src=\"".$tweet->user-> profile_image_url_https."\" ";
    $strList=$strList."name=\"".$tweet->user-> screen_name."\" ";
    $strList=$strList.">";
    $strList=$strList."</form>";
    $strList=$strList."</div>";

    $strList=$strList."<p>";
    $strList=$strList."<a href=\"http://twitter.com/".$tweet->user-> screen_name."\">".$tweet->user->name."</a>";
    $strList=$strList."</p>";

    $strList=$strList."<p>";
    $strList=$strList."  RT:".getRetweetCount($tweet->retweet_count) ;
    $strList=$strList." ☆:".getFavouritesCount($tweet->favourites_count) ;
    $strList=$strList."<h2>";
    $strList=$strList." ".getTime($tweet-> created_at) ;
    $strList=$strList."</h2>";
    $strList=$strList."</p>";
    $strList=$strList."</li>";
    $strList=$strList."</div>";
    $strList=$strList."<div id=\"user_text\">";
    $strList=$strList."<li>";
    $strList=$strList."<p>".getTextLine($tweet-> text)."</p>";
//    $strList=$strList.getTextLine($tweet-> text);
    $strList=$strList."</li>";
    $strList=$strList."</div>";
    return $strList;
}

function ViewHeder(){
      $params = array(
      'screen_name' => $_SESSION['me']['tw_screen_name']
      );

    $userShow = getUserShow($params);
    $strList="<ui>";

    $strList=$strList."<div onclick=\"obj=document.getElementById('menu').style; obj.display=(obj.display=='none')?'block':'none';\">";
    $strList=$strList."<a style=\"cursor:pointer;\">";
    $strList=$strList."<img alt=\"".$userShow-> screen_name."\" src=\"".$userShow-> profile_image_url_https."\" >";
    $strList=$strList."</a>";
    $strList=$strList."</div>";
    $strList=$strList."<div id=\"menu\" style=\"display:none;clear:both;\">";
    $strList=$strList."<li><p><a href=\"userlist.php\">フォロアー</a></p></li>";    
    $strList=$strList."<li><p><a href=\"logout.php\">[ログアウト]</a></p></li>";    
    $strList=$strList."</div>";

    $strList=$strList."<li>";
    $strList=$strList.$userShow->name;
    $strList=$strList."</li>";
    $strList=$strList."</ul>";
    echo $strList;

}

function ViewText($tweets){
    $strList="";
    foreach ($tweets as $tweet){
        $items = $tweet->entities->urls;
        $medias = $tweet->entities->media;
        $one = true;

        //公式画像
        foreach ($medias as $media){
           if($media-> media_url){
                if($one){
                        $strList=$strList.viewTextList($tweet);
                        $one =false;
                }
                $strList=$strList."<div id=\"user_image\"><a href=" . $media-> media_url . ">".
                "<img src= " . $media-> media_url.  "></a></div>";
            }
        }
        //twipic,instgramなど
        foreach ($items as $item){
           if(isImageURL($item-> expanded_url)){
                if($one){
                        $strList=$strList.viewTextList($tweet);
                        $one = false;
                }
                $strList=$strList."<div id=\"user_image\">".getImageURL($item-> expanded_url)."</div>";
            }
        }
        if(!$one)        $strList=$strList."</ul>";
    }
    echo  $strList; 
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
    <?php echo h(ViewHeder()); ?>
<!-- <p><?php echo h($_SESSION['me']['tw_screen_name']); ?>のTwitterアカウントでログインしています。</p>-->
<!-- <p><a href="logout.php">[ログアウト]</a></p>-->
<!--<p><出力件数：<?php echo h(count($tweets)); ?></p>-->
</div><!-- /heder -->
<div id="timeline">
<div id="main">
    <?php echo h(ViewText($tweets)); ?>
</div><!-- /main -->
</div><!-- /timeline -->
</body>
</html>