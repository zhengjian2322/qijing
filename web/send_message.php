<?php

namespace MRBS;

require "defaultincludes.inc";
require_once "mrbs_sql.inc";
require_once "functions_ical.inc";
require_once __DIR__ . "/src/SmsSenderUtil.php";
require_once __DIR__ . "/src/SmsSingleSender.php";
use Qcloud\Sms\SmsSingleSender;

// 短信应用SDK AppID
$appid = 1400225849; // 1400开头

// 短信应用SDK AppKey
$appkey = "1cbce7778165c23ffc03452181524a74";

// 需要发送短信的手机号码
$phoneNumbers = ["13121218136"];
$templateId = 218808;
$smsSign = "奇境";



// 单发短信
try {
    $ssender = new SmsSingleSender($appid, $appkey);
    $result = $ssender->send(0, "86", $phoneNumbers[0],
        "【奇境】您已预约成功。尊贵享受，身临奇境！奇境崇文门店恭候您光临！联系电话：15330095565，微信同号。", "", "");
    $rsp = json_decode($result);
    var_dump($rsp) ;
} catch(\Exception $e) {
    echo var_dump($e);
}
echo "\n";
