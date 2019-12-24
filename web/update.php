<?php

namespace MRBS;

use MRBS\Form\Form;

require "defaultincludes.inc";
require_once "mrbs_sql.inc";




// Check the user is authorised for this page
checkAuthorised();

// Get non-standard form variables
$type = get_form_var('type', 'string', null, INPUT_POST);


if ($type == "update_massagist_status") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $status = get_form_var('status', 'int', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($status) || ($status === '')) {
        fatal_error("更新状态失败");
    }
    convertMassagistStatus($id, $status);
    $returl = "massagist.php";
} else if ($type == "update_massagist_overtime") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $month = get_form_var('month', 'string', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($month) || ($month === '')) {
        fatal_error("增加加班时长失败");
    }
    if (empty($_POST['overtime']) || !is_numeric($_POST['overtime'])) {
        fatal_error("加班时长必须是整数或者小数");
    } else {
        $overtime = number_format((float)$_POST['overtime'], 2);
    }
    updateMassagistOvertime($id, $overtime, $month);
    $returl = "massagist.php";
} else if ($type == "minus_massagist_vacation") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $vacation = get_form_var('vacation', 'int', null, INPUT_POST);
    $month = get_form_var('month', 'string', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($vacation) || ($vacation === '') || !isset($month) || ($month === '')) {
        fatal_error("减少假期失败");
    }
    if ($vacation <= 0) {
        fatal_error("假期数不能为负数");
    }
    updateMassagistVacation($id, $vacation, $month);
    $returl = "massagist.php";
} else if ($type == "update_massagist_praise") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $month = get_form_var('month', 'string', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($month) || ($month === '')) {
        fatal_error("增加好评数失败");
    }
    if (empty($_POST['praise']) || !is_numeric($_POST['praise'])) {
        fatal_error("好评数必须是整数");
    }
    updateMassagistPraise($id, (int)$_POST['praise'], $month);
    $returl = "massagist.php";
} else if ($type == "update_admin_overtime") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $month = get_form_var('month', 'string', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($month) || ($month === '')) {
        fatal_error("增加加班时长失败");
    }
    if (empty($_POST['overtime']) || !is_numeric($_POST['overtime'])) {
        fatal_error("加班时长必须是整数或者小数");
    } else {
        $overtime = number_format((float)$_POST['overtime'], 2);
    }
    updateAdminOvertime($id, $overtime, $month);
    $returl = "admins.php";
} else if ($type == "minus_admin_vacation") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $vacation = get_form_var('vacation', 'int', null, INPUT_POST);
    $month = get_form_var('month', 'string', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($vacation) || ($vacation === '') || !isset($month) || ($month === '')) {
        fatal_error("减少假期失败");
    }

    if ($vacation <= 0) {
        fatal_error("假期数不能为负数");
    }
    updateAdminVacation($id, $vacation, $month);
    $returl = "admins.php";
} else if ($type == "update_admin_praise") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $month = get_form_var('month', 'string', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($month) || ($month === '')) {
        fatal_error("增加好评数失败");
    }
    if (empty($_POST['praise']) || !is_numeric($_POST['praise'])) {
        fatal_error("好评数必须是整数");
    }
    updateAdminPraise($id, (int)$_POST['praise'], $month);
    $returl = "admins.php";
} else if ($type == "update_room_status") {
    $id = get_form_var('id', 'int', null, INPUT_POST);
    $status = get_form_var('status', 'int', null, INPUT_POST);
    if (!isset($id) || ($id === '') || !isset($status) || ($status === '')) {
        fatal_error("更新状态失败");
    }
    //convertRoomStatus($id, $status);
    $returl = "rooms.php";

} else if ($type == "update_order") {
    global $tbl_orders, $tbl_massagist;

    $id = get_form_var('order_id', 'int', null, INPUT_POST);
    $start_date = isset($_POST['start_date']) ? (empty($_POST['start_date']) ? null : $_POST['start_date']) : null;
    $start_seconds = isset($_POST['start_seconds']) ? (empty($_POST['start_seconds']) ? 0 : (int)$_POST['start_seconds']) : 0;
    $booking_date = isset($_POST['booking_date']) ? (empty($_POST['booking_date']) ? null : $_POST['booking_date']) : null;
    $booking_seconds = isset($_POST['booking_seconds']) ? (empty($_POST['booking_seconds']) ? 0 : (int)$_POST['booking_seconds']) : 0;
    $end_date = isset($_POST['end_date']) ? (empty($_POST['end_date']) ? null : $_POST['end_date']) : null;
    $end_seconds = isset($_POST['end_seconds']) ? (empty($_POST['end_seconds']) ? 0 : (int)$_POST['end_seconds']) : 0;
//    $booking_no = isset($_POST['booking_no']) ? (empty($_POST['booking_no']) ? null : $_POST['booking_no']) : null;
//    $booking_linkman = isset($_POST['booking_linkman']) ? (empty($_POST['booking_linkman']) ? null : $_POST['booking_linkman']) : null;
//    $booking_phone = isset($_POST['booking_phone']) ? (empty($_POST['booking_phone']) ? null : $_POST['booking_phone']) : null;
    $customer_type = isset($_POST['customer_type']) ? (empty($_POST['customer_type']) ? 0 : (int)$_POST['customer_type']) : 0;
    $order_source = isset($_POST['order_source']) ? (empty($_POST['order_source']) ? 0 : (int)$_POST['order_source']) : 0;
    $pay_way = isset($_POST['pay_way']) ? (empty($_POST['pay_way']) ? 0 : (int)$_POST['pay_way']) : 0;
    $order_amount = isset($_POST['order_amount']) ? (empty($_POST['order_amount']) ? 0 : (int)$_POST['order_amount']) : 0;
    $discount_amount = isset($_POST['discount_amount']) ? (empty($_POST['discount_amount']) ? 0 : (int)$_POST['discount_amount']) : 0;
    $paid_amount = isset($_POST['paid_amount']) ? (empty($_POST['paid_amount']) ? 0 : (int)$_POST['paid_amount']) : 0;
    $unpaid_amount = isset($_POST['unpaid_amount']) ? (empty($_POST['unpaid_amount']) ? 0 : (int)$_POST['unpaid_amount']) : 0;
    $project_id = isset($_POST['project_id']) ? (empty($_POST['project_id']) ? 0 : (int)$_POST['project_id']) : 0;
    $massagist = isset($_POST['massagist']) ? (empty($_POST['massagist']) ? null : $_POST['massagist']) : null;
    $customer_amount  = isset($_POST['customer_amount']) ? (empty($_POST['customer_amount']) ? null : $_POST['customer_amount']) : null;

    if (!isset($id) || ($id === '')) {
        fatal_error("更新状态失败");
    }
    if (!isset($massagist) || ($massagist === '')) {
        fatal_error("技师不能为空");
    }
    $room_id  = (int)$_POST['room_id'];
    $startTimestamp = date('Y-m-d H:i:s', strtotime($start_date) + $start_seconds);
    $endTimestamp = date('Y-m-d H:i:s', strtotime($end_date) + $end_seconds);
    $bookingTimestamp = date('Y-m-d H:i:s', strtotime($booking_date) + $booking_seconds);
    $yesterday = date("Y-m-d 00:00:00", strtotime("-1 day"));

    if ($startTimestamp >= $endTimestamp) {
        fatal_error("预约的起始时间不能超过终止时间");
    }
    $sql = "SELECT `id`,`start_timestamp`,`end_timestamp` FROM $tbl_orders WHERE `room_id` = ?   AND  `start_timestamp` > ? AND `id` != ? AND `display` = 1";
    $res = db()->query($sql, array($room_id, "$yesterday ",$id));
    $timestampSet = $res->all_rows_keyed();
    foreach ($timestampSet as $timestamp) {
        if ($startTimestamp >= $timestamp['end_timestamp'] || $endTimestamp <= $timestamp['start_timestamp']) {

        } else {
            fatal_error("该时间已经被预约");
        }
    }
    if (!empty($massagist) && is_array($massagist)) {
        $massagistIds = implode(',', $massagist);
        $sql = "UPDATE $tbl_massagist SET `status` = CASE `id`";
        foreach ($massagist as $k) {
            $sql .= " WHEN " . $k . " THEN 1 ";//置为忙碌状态
        }
        db()->command($sql . "END WHERE `id` IN ($massagistIds)");

        $sql = "UPDATE $tbl_orders SET `start_timestamp`=?,`end_timestamp`=?,`booking_timestamp`=?,`customer_type`=?,`order_source`=?,
      `pay_way`=?,`order_amount`=?,`discount_amount`=?,`paid_amount`=?,`unpaid_amount`=?
    ,`massagist`=? ,`customer_amount` = ? WHERE `id` = ?";
        db()->command($sql, array($startTimestamp,$endTimestamp ,$bookingTimestamp ,$customer_type ,$order_source ,$pay_way ,
            $order_amount ,$discount_amount ,$paid_amount ,$unpaid_amount , $massagistIds,$customer_amount,$id));
    }else{
        $sql = "UPDATE $tbl_orders SET `start_timestamp`=?,`end_timestamp`=? ,`booking_timestamp`=?, 
     `customer_type`=?,`order_source`=?,`pay_way`=?,`order_amount`=?,`discount_amount`=?,`paid_amount`=?,`unpaid_amount`=?
     ,`customer_amount` = ? WHERE `id` = ?";
        db()->command($sql, array($startTimestamp,$endTimestamp,$bookingTimestamp  ,$customer_type ,$order_source ,$pay_way ,
            $order_amount ,$discount_amount ,$paid_amount ,$unpaid_amount , $customer_amount,$id));
    }

//    convertRoomStatus($id, $status);
    $returl = "day.php";

}else if($type == 'change_massagist_rank'){
    global  $tbl_massagist;

    $id = get_form_var('edit_id', 'int', null, INPUT_POST);
    $rank = get_form_var('edit_rank', 'string', null, INPUT_POST);
    if(empty($id)){
        fatal_error("没有选择相应的技师");
    }
    if(empty($rank)){
        fatal_error("没有选择级别");
    }
    $sql = "UPDATE $tbl_massagist SET `rank` = ? WHERE `id` = ? ";
    db()->command($sql, array( $rank,$id ));

    $returl = "massagist.php";
}else if($type == 'change_admin_role'){
    global  $tbl_admin;
    $id = get_form_var('edit_id', 'int', null, INPUT_POST);
    $duty = get_form_var('duty', 'string', null, INPUT_POST);
    if(empty($id)){
        fatal_error("没有选择相应的管理员");
    }
    if(empty( $duty)){
        fatal_error("没有选择职务");
    }
    $sql = "UPDATE $tbl_admin  SET `duty` = ? WHERE `id` = ? ";
    db()->command($sql, array( $duty,$id ));

    $returl = "admins.php";
}else if($type == 'invisible_order'){
    global  $tbl_orders,$tbl_massagist;
    $id = get_form_var('order_id', 'int', null, INPUT_POST);
    $sql = "SELECT `start_timestamp`,`end_timestamp`,`massagist` FROM $tbl_orders WHERE `id` = ? ";
    $res = db()->query($sql, array($id ));
    $timestampSet = $res->all_rows_keyed();

    $currentTime = date('Y-m-d H:i:s', time());
    if($timestampSet[0]['end_timestamp'] >= $currentTime ){
        if(!empty($timestampSet[0]['massagist'])){
            $massagist = $timestampSet[0]['massagist'];
            db()->command( "UPDATE $tbl_massagist SET `status` = 0 WHERE `id` IN ($massagist)");
        }
    }

    $sql = "UPDATE $tbl_orders  SET `display` = 0 WHERE `id` = ? ";
    db()->command($sql, array( $id ));
    $returl = "day.php";
}

header("Location: $returl");

