<?php

include("_configs.php");
if(empty($JIO_AUTH)) { exit("Please Login Jio Account"); }

header("Access-Control-Allow-Origin: *");

$id = "";
if(isset($_REQUEST['id'])) { $id = $_REQUEST['id']; }
if(empty($id)) { exit("Channel ID Missing"); }
$channel_id = $id;

$tvChannelHeaders = array(
    'appkey: NzNiMDhlYzQyNjJm',
    'devicetype: phone',
    'os: android',
    'deviceid: ' . $deviceId,
    'osversion: 9',
    'uniqueid: ' . $JIO_AUTH['user']['unique'],
    'usergroup: tvYR7NSNn7rymo3F',
    'languageid: 6',
    'userid: ' . $JIO_AUTH['user']['subscriberId'],
    'crmid: ' . $JIO_AUTH['user']['subscriberId'],
    'isott: false',
    'channel_id: ' . $channel_id,
    'ssotoken: ' . $JIO_AUTH['ssotoken'],
    'subscriberid: ' . $JIO_AUTH['user']['subscriberId'],
    'lbcookie: 1',
    'versioncode: 282',
    'content-type: application/x-www-form-urlencoded',
    'user-agent: okhttp/3.14.9'
);
$process = curl_init("https://tv.media.jio.com/apis/v2.0/getchannelurl/getchannelurl?langId=6");
curl_setopt($process, CURLOPT_POST, 1);
curl_setopt($process, CURLOPT_POSTFIELDS, 'channel_id=' . $channel_id);
curl_setopt($process, CURLOPT_HTTPHEADER, $tvChannelHeaders);
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_ENCODING, '');
curl_setopt($process, CURLOPT_TIMEOUT, 10);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
$return = curl_exec($process);
curl_close($process);
$data = json_decode($return, true);

if (isset($data['result']) && !empty($data['result'])) {
    if (stripos($data['result'], '.m3u8') !== false) {
        $hlsUrl = $data['result'];
    }
}

if (!empty($hlsUrl)) {
    if (isset($data['bitrates']['auto']) && !empty($data['bitrates']['auto'])) {
        if (stripos($data['bitrates']['auto'], ".m3u8") !== false) {
            $hlsUrl = $data['bitrates']['auto'];
        }
    }
}

if(empty($hlsUrl))
{
    exit("Failed To Get Channel URL - Check Channel ID Please");
}

$playurl = ""; $playcookies = "";
$proceka = explode("__hdnea__", $hlsUrl);
if(isset($proceka[0]))
{
    $playurl = $proceka[0];
}
if(isset($proceka[1]))
{
    $playcookies = "__hdnea__".$proceka[1];
}

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
                 "Cookie: " . $playcookies,
                 "channelid: " . $channel_id);
$masterlist = $hlsUrl;
$process = curl_init($masterlist);
curl_setopt($process, CURLOPT_HTTPHEADER, $haeders);
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_ENCODING, '');
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($process);
$cdnurl = curl_getinfo($process, CURLINFO_EFFECTIVE_URL);
curl_close($process);
$bdnurl = explode("?", $cdnurl);
$baseurl = str_replace(basename($bdnurl[0]), "", $bdnurl[0]);
if(stripos($result, "#EXTM3U") !== false)
{
    $hline = "";
    $vline = explode("\n", $result);
    foreach ($vline as $iline)
    {
        if (stripos($iline, "?") !== false) { $connr = "&"; } else { $connr = "?";}
        if (stripos($iline, 'URI="') !== false)
        {
            $hline .= str_replace('URI="', 'URI="playlist.php?c='.base64_encode(base64_encode($playcookies)).'&l=' .$baseurl. '', $iline) . "\n";
        }
        else
        {
            if(stripos($iline, ".m3u8") !== false && stripos($iline, 'URI="') === false)
            {
                $hline .= "playlist.php?c=".base64_encode(base64_encode($playcookies))."&l=".$baseurl.$iline. "\n";
            }
            else
            {
                $hline .= $iline . "\n";
            }
        }
    }
    if(!empty($hline))
    {
        header("Content-Type: application/vnd.apple.mpegurl");
        exit(trim($hline));
    }
}

http_response_code(503);
exit("Streamer Unavailable");

?>