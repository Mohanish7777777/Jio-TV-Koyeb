<?php

error_reporting(0);

$SAVE_JIO_LOGIN_HERE = "assets/_secure/logindata.cxt";

//========================================================================================//
//          D O     N O T      E D I T      A N Y T H I N G     B E L O W
//========================================================================================//

$streamenvproto = "http";
if(isset($_SERVER['HTTPS'])){ if($_SERVER['HTTPS'] == "on"){ $streamenvproto = "https"; } }
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])){ if($_SERVER['HTTP_X_FORWARDED_PROTO'] == "https"){ $streamenvproto = "https"; }}

if(stripos($_SERVER['HTTP_HOST'], ':') !== false)
{
    $warl = explode(':', $_SERVER['HTTP_HOST']);
    if(isset($warl[0]) && !empty($warl[0])){ $_SERVER['HTTP_HOST'] = trim($warl[0]); }
}
if(stripos($_SERVER['HTTP_HOST'], 'localhost') !== false){ $_SERVER['HTTP_HOST'] = str_replace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']); }
$local_ip = getHostByName(php_uname('n'));
if($_SERVER['SERVER_ADDR'] !== "127.0.0.1"){ $plhoth = $_SERVER['HTTP_HOST'];  }else{ $plhoth = $local_ip;  }
$plhoth = str_replace(" ", "%20", $plhoth);

//========================================================================================//

function createplaylistfile($channels)
{
    global $streamenvproto;
    global $plhoth;
    $IPLAY = '#EXTM3U x-tvg-url="https://t.ly/tWri"' . "\n";
    $v = 0;
    foreach($channels as $mere)
    {
        $v++;
        $IPLAY .= '#EXTINF:-1 tvg-id="'.$mere['id'].'" tvg-name="'.$mere['title'].'" tvg-country="IN" tvg-logo="'.$mere['logo'].'" tvg-chno="'.$mere['id'].'" group-title="'.$mere['category'].' - '.$mere['language'].'",'.$mere['title']."\n";
        $IPLAY .= $streamenvproto."://".$plhoth.str_replace(" ", "%20", str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']))."master.php?id=".$mere['id']."&e=.m3u8\n";
    }
    if(!empty($IPLAY))
    {
        if(file_put_contents("tvplaylist.m3u", trim($IPLAY))){}
    }    
}

function jio_category($c)
{
    $category = array('5' => 'Entertainment', '6' => 'Movies', '7' => 'Kids', '8' => 'Sports', '9' => 'Lifestyle', '10' => 'Infotainment', '12' => 'News', '13' => 'Music', '15' => 'Devotional', '16' => 'Business', '17' => 'Education', '19' => 'Devotional');
    if(isset($category[$c])){ return $category[$c]; }else{ return ""; }
}

function jio_language($l)
{
    $language = array('1' => 'Hindi', '2' => 'Marathi', '3' => 'Punjabi', '4' => 'Urdu', '5' => 'Bengali', '6' => 'English', '7' => 'Malayalam', '8' => 'Tamil', '9' => 'Gujarati', '10' => 'Odia', '11' => 'Telugu', '12' => 'Bhojpuri', '13' => 'Kannada', '14' => 'Assamese', '15' => 'Nepali', '16' => 'French');
    if(isset( $language[$l])){ return  $language[$l]; }else{ return ""; }
}


$JIO_AUTH = array();
$deviceId = substr(sha1("usftoolshub".time()), 0, 16);

if(file_exists($SAVE_JIO_LOGIN_HERE))
{
    $reajo = @json_decode(@file_get_contents($SAVE_JIO_LOGIN_HERE), true);
    if(isset($reajo['ssotoken']) && !empty($reajo['ssotoken']))
    {
        $JIO_AUTH = $reajo;
    }
}

function login_response($status, $message)
{
    $response = array("status" => $status, "message" => $message);
    exit(json_encode($response));
}

?>