<?php

include("_configs.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$getme = "";

if(isset($_REQUEST['getme'])) { $getme = $_REQUEST['getme']; }

if($getme == "m3uplaylist")
{
    $link = $streamenvproto."://".$plhoth.str_replace(" ", "%20", str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']))."tvplaylist.m3u?hash=".md5(time());
    login_response("success", $link);
}
elseif($getme == "channels")
{
    $items = array();
    $apiurl = 'http://jiotv.data.cdn.jio.com/apis/v1.3/getMobileChannelList/get/?langId=6&os=android&devicetype=phone&usergroup=tvYR7NSNn7rymo3F&version=6.0.9&langId=6';
    $apiheads = array('usergroup: tvYR7NSNn7rymo3F', 'appkey: NzNiMDhlYzQyNjJm', 'User-Agent: okhttp/3.14.9', 'os: android', 'devicetype: phone', 'appversioncode: 262', 'appversion: 6.0.9');
    $process = curl_init($apiurl);
    curl_setopt($process, CURLOPT_HTTPHEADER, $apiheads);  
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_ENCODING, '');
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($process);
    curl_close($process);
    if(!empty($return))
    {
        $imoi = @json_decode($return, true);
        if(isset($imoi['result']) && !empty($imoi['result']))
        {
            foreach($imoi['result'] as $intv)
            {
                if(stripos($intv['channel_name'], 'test') === false)
                {
                    if(stripos($intv['channel_name'], 'Star') === false)
                    {
                        $items[] = array('id' => $intv['channel_id'],
                                         'title' => $intv['channel_name'],
                                         'slug' => str_replace('.png', '', $intv['logoUrl']),
                                         'logo' => 'http://jiotv.catchup.cdn.jio.com/dare_images/images/'.$intv['logoUrl'],
                                         'category' => str_replace(null, '', jio_category($intv['channelCategoryId'])),
                                         'language' => str_replace(null, '', jio_language($intv['channelLanguageId'])));
                    }
                }
            }
        }
    }
    createplaylistfile($items);
    exit(json_encode($items));
}
elseif($getme == "search")
{
    $q = ""; $items = array();
    if(isset($_REQUEST['q'])){ $q = trim($_REQUEST['q']); }
    if(empty($q)){ exit(); }
    $apiurl = 'http://jiotv.data.cdn.jio.com/apis/v1.3/getMobileChannelList/get/?langId=6&os=android&devicetype=phone&usergroup=tvYR7NSNn7rymo3F&version=6.0.9&langId=6';
    $apiheads = array('usergroup: tvYR7NSNn7rymo3F', 'appkey: NzNiMDhlYzQyNjJm', 'User-Agent: okhttp/3.14.9', 'os: android', 'devicetype: phone', 'appversioncode: 262', 'appversion: 6.0.9');
    $process = curl_init($apiurl);
    curl_setopt($process, CURLOPT_HTTPHEADER, $apiheads);  
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_ENCODING, '');
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($process);
    curl_close($process);
    if(!empty($return))
    {
        $imoi = @json_decode($return, true);
        if(isset($imoi['result']) && !empty($imoi['result']))
        {
            foreach($imoi['result'] as $intv)
            {
                if(stripos($intv['channel_name'], 'test') === false)
                {
                    if(stripos($intv['channel_name'], 'Star') === false)
                    {
                        if(stripos($intv['channel_name'], $q) !== false)
                        {
                            $items[] = array('id' => $intv['channel_id'],
                                             'title' => $intv['channel_name'],
                                             'slug' => str_replace('.png', '', $intv['logoUrl']),
                                             'logo' => 'http://jiotv.catchup.cdn.jio.com/dare_images/images/'.$intv['logoUrl'],
                                             'category' => str_replace(null, '', jio_category($intv['channelCategoryId'])),
                                             'language' => str_replace(null, '', jio_language($intv['channelLanguageId'])));
                        }
                    }
                }
            }
        }
    }
    exit(json_encode($items));
}
elseif($getme == "login1")
{
    $do = ""; $idn = ""; $otp = "";
    if(isset($_REQUEST['do'])){ $do = $_REQUEST['do']; }
    if(isset($_REQUEST['idn'])){ $idn = $_REQUEST['idn']; }
    if(isset($_REQUEST['otp'])){ $otp = $_REQUEST['otp']; }
    if(empty($idn)) {
        login_response("error", "Please Enter Jio Mobile Number");
    }
    if($do == "send")
    {
        $zm_api = 'https://jiotvapi.media.jio.com/userservice/apis/v1/loginotp/send';
        $zm_headers = array('appname: RJIL_JioTV', 'os: android', 'devicetype: phone', 'content-type: application/json', 'user-agent: okhttp/3.14.9');
        $zm_payload = array('number' => base64_encode('+91'.$idn));
        $process = curl_init($zm_api);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, json_encode($zm_payload));
        curl_setopt($process, CURLOPT_HTTPHEADER, $zm_headers); 
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 10);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $zm_resp = curl_exec($process);
        $zm_info = curl_getinfo($process);
        curl_close($process);
        $zm_data = @json_decode($zm_resp, true);
        if($zm_info['http_code'] == 204)
        {
            login_response("success", "OTP Sent Successfully");
        }
        else
        {
            if(isset($zm_data['message']))
            {
                login_response("error", $zm_data['message']);
            }
            else
            {
                login_response("error", "Something Went Wrong");
            }
        }
    }
    else
    {
        if(empty($otp)) { login_response("error", "Please Enter OTP"); }
        $zm_api = 'https://jiotvapi.media.jio.com/userservice/apis/v1/loginotp/verify';
        $zm_headers = array('appname: RJIL_JioTV', 'os: android', 'devicetype: phone', 'content-type: application/json', 'user-agent: okhttp/3.14.9');
        $zm_payload = '{"number":"'.base64_encode('+91'.$idn).'","otp":"'.$otp.'","deviceInfo":{"consumptionDeviceName":"RMX1945","info":{"type":"android","platform":{"name":"RMX1945"},"androidId":"'.substr(md5("Wevwev".time()), 0, 16).'"}}}';
        $process = curl_init($zm_api);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $zm_payload);
        curl_setopt($process, CURLOPT_HTTPHEADER, $zm_headers); 
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 10);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $zm_resp = curl_exec($process);
        $zm_info = curl_getinfo($process);
        curl_close($process);
        $zm_data = @json_decode($zm_resp, true);
        if(isset($zm_data['ssoToken']) && !empty($zm_data['ssoToken']))
        {
            $save_jio = array('ssotoken' => $zm_data['ssoToken'], 'user' => $zm_data['sessionAttributes']['user']);
            if(file_put_contents($SAVE_JIO_LOGIN_HERE , json_encode($save_jio)))
            {
                login_response("success", "Logged In Successfully");
            }
            else
            {
                login_response("error", "Logged In Successfully But Failed To Save Data");
            }
        }
        else
        {
            if(isset($zm_data['message']))
            {
                login_response("error", "Jio Error : ".$zm_data['message']);
            }
            else
            {
                login_response("error", "Failed To Verify OTP");
            }
        }
    }
}
elseif($getme == "login2")
{
    $idn = ""; $pass = "";
    if(isset($_REQUEST['idn'])) {
        $idn = $_REQUEST['idn'];
    }
    if(isset($_REQUEST['pass'])) {
        $pass = $_REQUEST['pass'];
    }
    if(empty($idn)) {
        login_response("error", "Please Enter Jio Mobile or Email Address");
    }
    if(empty($pass)) {
        login_response("error", "Please Enter Password");
    }
    $zm_api = 'https://api.jio.com/v3/dip/user/unpw/verify';
    $zm_headers = array('user-agent: okhttp/3.14.9', 'os: android', 'devicetype: phone', 'content-type: application/json', 'x-api-key: l7xx938b6684ee9e4bbe8831a9a682b8e19f');
    $zm_payload = array('identifier' => $idn,
                        'password' => $pass,
                        'rememberUser' => 'T',
                        'upgradeAuth' => 'Y',
                        'returnSessionDetails' => 'T',
                        'deviceInfo' => array('consumptionDeviceName' => 'samsung SM-G930F',
                                              'info' => array('type' => 'android',
                                                              'platform' => array('name' => 'SM-G930F',
                                                                                  'version' => '5.1.1'),
                                                               'androidId' => $deviceId)));
    $process = curl_init($zm_api);
    curl_setopt($process, CURLOPT_POST, 1);
    curl_setopt($process, CURLOPT_POSTFIELDS, json_encode($zm_payload));
    curl_setopt($process, CURLOPT_HTTPHEADER, $zm_headers); 
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $zm_resp = curl_exec($process);
    curl_close($process);
    $zm_data = @json_decode($zm_resp, true);
    if(isset($zm_data['ssoToken']) && !empty($zm_data['ssoToken']))
    {
      $save_jio = array('ssotoken' => $zm_data['ssoToken'], 'user' => $zm_data['sessionAttributes']['user']);
      if(file_put_contents($SAVE_JIO_LOGIN_HERE , json_encode($save_jio)))
      {
        login_response("success", "Logged In Successfully");
      }
      else
      {
        login_response("error", "Logged In Successfully But Failed To Save Data");
      }
    }
    else
    {
        login_response("error", "Failed To Login. Check Credentials");
    }
}
elseif($getme == "isLoggedIn")
{
    $isLoggedIn = false;
    if(file_exists($SAVE_JIO_LOGIN_HERE)) {
        $reajo = @json_decode(@file_get_contents($SAVE_JIO_LOGIN_HERE), true);
        if(isset($reajo['ssotoken']) && !empty($reajo['ssotoken'])){
            $isLoggedIn = true;
        }
    }
    if($isLoggedIn == true)
    {
        login_response("success", "Jio Logged In");
    }
    else
    {
        login_response("error", "Jio Logged Out");
    }
}
elseif($getme == "logout")
{
    $isLoggedOut = false;
    if(file_exists($SAVE_JIO_LOGIN_HERE))
    {
        if(unlink($SAVE_JIO_LOGIN_HERE))
        {
            $isLoggedOut = true;
        }
    }
    else
    {
        $isLoggedOut = true;
    }
    if($isLoggedOut == true)
    {
        login_response("success", "Jio Logged Out");
    }
    else
    {
        login_response("error", "Jio Log Out Failed");
    }
}

?>