<?php
namespace MRBS;

require "defaultincludes.inc";
require_once "mrbs_sql.inc";
require_once "functions_ical.inc";

use MRBS\Form\Form;
use MRBS\Form\ElementInputSubmit;

function invalid_booking($message)
{
  global $day, $month, $year, $area, $room;
  
  print_header($day, $month, $year, $area, isset($room) ? $room : null);
  echo "<h1>" . get_vocab('invalid_booking') . "</h1>\n";
  echo "<p>$message</p>\n";
  // Print footer and exit
  print_footer(true);
}

$ajax = get_form_var('ajax', 'int');
if ($ajax && !checkAuthorised(true))
{
  exit;
}

// (1) Check the user is authorised for this page
//  ---------------------------------------------
checkAuthorised();

// Also need to know whether they have admin rights
$user = getUserName();

$start_date = isset($_POST['start_date']) ? (empty($_POST['start_date']) ? null: $_POST['start_date']) : null;
$start_seconds = isset($_POST['start_seconds']) ? (empty($_POST['start_seconds']) ? 0: (int)$_POST['start_seconds']) : 0;
$booking_date = isset($_POST['booking_date']) ? (empty($_POST['booking_date']) ? null: $_POST['booking_date']) : null;
$booking_seconds = isset($_POST['booking_seconds']) ? (empty($_POST['booking_seconds']) ? 0: (int)$_POST['booking_seconds']) : 0;
$end_date = isset($_POST['end_date']) ? (empty($_POST['end_date']) ? null: $_POST['end_date']) : null;
$end_seconds = isset($_POST['end_seconds']) ? (empty($_POST['end_seconds']) ? 0: (int)$_POST['end_seconds']) : 0;
$booking_no = isset($_POST['booking_no']) ? (empty($_POST['booking_no']) ? null: $_POST['booking_no']) : null;
$customer_amount = isset($_POST['customer_amount']) ? (empty($_POST['customer_amount']) ? 0: (int)$_POST['customer_amount']) : 0;
$booking_linkman = isset($_POST['booking_linkman']) ? (empty($_POST['booking_linkman']) ? null: $_POST['booking_linkman']) : null;
$booking_phone = isset($_POST['booking_phone']) ? (empty($_POST['booking_phone']) ? null: $_POST['booking_phone']) : null;
$customer_type = isset($_POST['customer_type']) ? (empty($_POST['customer_type']) ? 0: (int)$_POST['customer_type']) : 0;
$order_source = isset($_POST['order_source']) ? (empty($_POST['order_source']) ? 0: (int)$_POST['order_source']) : 0;
$pay_way = isset($_POST['pay_way']) ? (empty($_POST['pay_way']) ? 0:(int) $_POST['pay_way']) : 0;
$order_amount = isset($_POST['order_amount']) ? (empty($_POST['order_amount']) ? 0: (int)$_POST['order_amount']) : 0;
$discount_amount = isset($_POST['discount_amount']) ? (empty($_POST['discount_amount']) ? 0: (int)$_POST['discount_amount']) : 0;
$paid_amount = isset($_POST['paid_amount']) ? (empty($_POST['paid_amount']) ? 0: (int)$_POST['paid_amount']) : 0;
$unpaid_amount = isset($_POST['unpaid_amount']) ? (empty($_POST['unpaid_amount']) ? 0: (int)$_POST['unpaid_amount']) : 0;
$project_id = isset($_POST['project_id']) ? (empty($_POST['project_id']) ? 0: (int)$_POST['project_id']) : 0;

if(!isset($_POST['room_id']) || empty($_POST['room_id'])){
    fatal_error("房间id不能为空");
}
global  $tbl_orders,$tbl_massagist;

$room_id =(int)$_POST['room_id'];
$startTimestamp = date('Y-m-d H:i:s',strtotime($start_date) + $start_seconds);
$endTimestamp = date('Y-m-d H:i:s',strtotime($end_date) + $end_seconds);
$bookingTimestamp = date('Y-m-d H:i:s',strtotime($booking_date) + $booking_seconds);
$yesterday  = date("Y-m-d 00:00:00",strtotime("-1 day"));

if($startTimestamp >= $endTimestamp){
    fatal_error("预约的起始时间不能超过终止时间");
}
if(!preg_match("/^1[34578]\d{9}$/", $booking_phone)){
    fatal_error("手机号码格式不正确");
}
$sql = "SELECT `id`,`start_timestamp`,`end_timestamp` FROM $tbl_orders WHERE `room_id` = ?   AND  `start_timestamp` > ? AND `display` = 1";
$res = db()->query($sql, array($room_id, "$yesterday " ));
$timestampSet = $res->all_rows_keyed();
foreach($timestampSet as $timestamp){
    if( $startTimestamp >= $timestamp['end_timestamp'] ||  $endTimestamp <= $timestamp['start_timestamp'] ){

    }else{
        fatal_error("该时间已经被预约");
    }
}
$currentTime = date('Y-m-d H:i:s', time());
if($currentTime < $startTimestamp){
    $status = 3;
}else if($currentTime >  $endTimestamp){
    $status = 1;
}else{
    $status = 2;
}



$sql = "INSERT INTO $tbl_orders (id,room_id,start_timestamp ,end_timestamp ,booking_no ,booking_timestamp,
                            booking_linkman,booking_phone,customer_type ,order_source ,pay_way , 
                            order_amount ,discount_amount ,paid_amount ,unpaid_amount  ,project_id,massagist,customer_amount,display,status) VALUES 
                            (DEFAULT,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NULL,?,1,?)";
db()->command($sql,array($room_id, $startTimestamp ,$endTimestamp ,"$booking_no",$bookingTimestamp , "$booking_linkman","$booking_phone",$customer_type ,$order_source ,$pay_way ,
    $order_amount ,$discount_amount ,$paid_amount ,$unpaid_amount,$project_id,$customer_amount,$status));


$insertId = db()->insert_id($tbl_orders, "id");
$param = explode('-', $start_date);
$returl = "day.php?year=$param[0]&month=$param[1]&day=$param[2]";
header("Location: $returl");
