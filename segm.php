<?php

include("_configs.php");
if(empty($JIO_AUTH)) { exit("Please Login Jio Account"); }

header("Access-Control-Allow-Origin: *");

$link = ""; $cookies = "";
if(isset($_REQUEST['l']))
{
    $link = $_REQUEST['l'];
}
if(isset($_REQUEST['c']))
{
    $cookies = $_REQUEST['c'];
}

if(empty($link))
{
    exit("Playlist Link Missing");
}

if(empty($cookies))
{
    exit("Playlist Cookies Missing");
}

$cookies = base64_decode(base64_decode($cookies));

$channel_id = 0;
$serielno = date('y').date('m').date('d').$channel_id.'000';
$haeders = array("os: android",
                 "subscriberId: ".$JIO_AUTH['user']['subscriberId'],
                 "deviceId: ".$deviceId,
                 "userId: ".$JIO_AUTH['user']['uid'],
                 "versionCode: 290",
                 "devicetype: phone",
                 "crmid: ".$JIO_AUTH['user']['subscriberId'],
                 "osVersion: 9",
                 "srno: ".$serielno,
                 "usergroup: tvYR7NSNn7rymo3F",
                 "uniqueId: ".$JIO_AUTH['user']['unique'],
                 "User-Agent: plaYtv/7.0.8 (Linux;Android 9) ExoPlayerLib/2.11.7",
                 "ssotoken: ".$JIO_AUTH['ssotoken'],
                 "Cookie: " . $cookies,
                 "channelid: " . $channel_id);

$slaveList = $link . "?" . $cookies;
$process = curl_init($slaveList);
curl_setopt($process, CURLOPT_HTTPHEADER, $haeders);
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_ENCODING, '');
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($process);
$efxlink = curl_getinfo($process, CURLINFO_EFFECTIVE_URL);
$httpcode = curl_getinfo($process, CURLINFO_HTTP_CODE);
curl_close($process);
$bavix = explode("?", $efxlink);
$baseurl = str_replace(basename($bavix[0]), "", $bavix[0]);

if($httpcode == 200 || $httpcode == 206)
{
    if(stripos($link, ".key") !== false) { $content_type = "application/binary"; }else { $content_type = "video/mp2t"; }
    header("Content-Type: ".$content_type);
    exit($result);
}
else
{
    http_response_code(404);
    exit();
}

?>