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
curl_close($process);
$bavix = explode("?", $efxlink);
$baseurl = str_replace(basename($bavix[0]), "", $bavix[0]);
if(stripos($result, "#EXTM3U") !== false)
{
    $hline = "";
    $vline = explode("\n", $result);
    foreach ($vline as $iline) 
    {
        if(stripos($iline, 'AES-128,URI="'))
        {
            $xsa = explode('AES-128,URI="', $iline);
            if(isset($xsa[1]))
            {
                $xsb = explode('"', $xsa[1]);
                if(isset($xsb[0]))
                {
                    $keyserver = $xsb[0];
                    $safkey = "segm.php?c=".base64_encode(base64_encode($cookies))."&l=".$keyserver;
                    $hline .= str_replace($keyserver, $safkey, $iline) . "\n";
                }
            }
        }
        else
        {
            if (stripos($iline, ".ts") !== false || stripos($iline, ".aac") !== false)
            {
                $hline .= "segm.php?c=".base64_encode(base64_encode($cookies))."&l=".$baseurl. $iline."\n";
            }
            else
            {
                $hline .= $iline . "\n";
            }
        }
    }
    header("Content-Type: application/vnd.apple.mpegurl");
    exit(print($hline));
}
else
{
    http_response_code(500);
    exit();
}

?>