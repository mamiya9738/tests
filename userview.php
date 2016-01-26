<?php
 
require_once('config.php');
require_once('codebird.php'); 
 
 session_start();

//print_r($_POST['user_name']);
//print_r($_POST);
$user_name = "";

// input image でPOSTにデータを入れる事が出来なかったのでnameに入れて取り出す
{
  $post = $_POST;
  $data = "";
  foreach($post as $key=>$value){
  if(empty($data))
      {
          $data = $key;
      }
  }
  //print_r(strlen($data));
  //print_r($data);
  $user_name = substr($data,0,strlen($data)-2);
//  print_r($user_name);

}
//ここまで

if (empty($user_name)) {
    header('Location: '.SITE_URL.'logon.php');

    exit;
}


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
                      "<img src= " . $url. "media/?size=t></a>";
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

//項目ごとの一覧
function viewTextList($tweet){
    $strList="<ul>";
//ツイートアイコン
    $strList=$strList."<li><a href=\"http://twitter.com/".$tweet->user-> screen_name."\"><img src=".$tweet->user-> profile_image_url_https."></a>";
    $strList=$strList.$tweet->user->name;
    $strList=$strList."  RT:".getRetweetCount($tweet->retweet_count) ;
    $strList=$strList." ☆:".getFavouritesCount($tweet->favourites_count) ;
    $strList=$strList." ".getTime($tweet-> created_at) ;
    $strList=$strList."</li>";
//    $strList=$strList."<li>".$tweet-> text."</li>";
    $strList=$strList."<li>".getTextLine($tweet-> text)."</li>";
    return $strList;
}

function ViewText($tweets){
    $strList="";
    foreach ($tweets as $tweet){
        $items = $tweet->entities->urls;
        $media = $tweet->entities->media;
        $one = true;

        //公式画像
        foreach ($media as $media){
           if($media-> media_url){
                if($one){
                        $strList=$strList.viewTextList($tweet);
                        $one =false;
                }
                $strList=$strList."<li id=\"g\"><a href=" . $media-> media_url . ">".
                "<img src= " . $media-> media_url.  "></a></li>";
            }
        }
        //twipic,instgramなど
        foreach ($items as $item){
           if(isImageURL($item-> expanded_url)){
                if($one){
                        $strList=$strList.viewTextList($tweet);
                        $one =false;
                }
                $strList=$strList."<li id=\"g\">".getImageURL($item-> expanded_url)."</li>";
            }
        }
        if(!$one)        $strList=$strList."</ul>";
    }
    echo  $strList; 
}

\Codebird\Codebird::setConsumerKey(CONSUMER_KEY, CONSUMER_SECRET);
$cb = \Codebird\Codebird::getInstance();
 
$cb->setToken($_SESSION['me']['tw_access_token'], $_SESSION['me']['tw_access_token_secret']);
 
$tweets = (array) $cb->statuses_userTimeline(array("count"=>"200", "include_entities" => "true", "screen_name"=>$user_name)); 

array_pop($tweets);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial=1.0">
    <title>テストインデックス</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.8.1/build/cssreset/cssreset-min.css">
    <link rel="stylesheet" href="mystyle.css">
</head>
<body>
<div id="heder">
 <p><?php echo h($_POST['user_name']); ?>を表示しています。</p>
<?php echo h($_SESSION['me']['profile_image_url_https']); ?>
<p><a href="logout.php">[ログアウト]</a></p>
<!--<p><出力件数：<?php echo h(count($tweets)); ?></p>-->
</div><!-- /heder -->
<div id="timeline">
<div id="main">
                <?php echo h(ViewText($tweets)); ?>
</div><!-- /main -->
</div><!-- /timeline -->
</body>
</html>