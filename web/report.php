<?php

namespace MRBS;

use MRBS\Form\ElementDiv;
use MRBS\Form\FieldButton;
use MRBS\Form\Form;
use MRBS\Form\ElementFieldset;
use MRBS\Form\ElementInputImage;
use MRBS\Form\Element;
use MRBS\Form\FieldInputText;
use MRBS\Form\FieldInputSubmit;
use MRBS\Form\FieldSelect;

require "defaultincludes.inc";




print_header();
$filePath = "C:\Users\zhengjian\Desktop\\";
function order_form()
{
    $form = new Form();

    $attributes = array(
        'class' => 'form_admin standard',
        'action' => 'report.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'export_orders_report');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend('导出订单报表');

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("起始日期")
        ->setControlAttributes(array('id' => 'export_start',
            'name' => 'export_start',
            'required' => TRUE,
            'autocomplete' => "off"));
    $div->addElement($field);

    // The description field
    $field = new FieldInputText();
    $field->setLabel("终止日期")
        ->setControlAttributes(array('id' => 'export_end',
            'required' => TRUE,
            'name' => 'export_end',
            'autocomplete' => "off"));
    $div->addElement($field);
    $fieldset->addElement($div);
    $field = new FieldInputSubmit();
    $field->setControlAttributes(array('value' => "导出报表",
        'class' => 'submit'));
    $fieldset->addElement($field);
    $form->addElement($fieldset);

    $form->render();
}

function massagist_form()
{
    $form = new Form();

    $attributes = array(
        'class' => 'form_admin standard',
        'action' => 'report.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'export_massagist_report');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend(('导出技师报表'));

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("起始日期")
        ->setControlAttributes(array('id' => 'massagist_start',
            'required' => TRUE,
            'name' => 'massagist_start',
            'autocomplete' => "off"));
    $div->addElement($field);

    // The description field
    $field = new FieldInputText();
    $field->setLabel("终止日期")
        ->setControlAttributes(array('id' => 'massagist_end',
            'required' => TRUE,
            'name' => 'massagist_end',
            'autocomplete' => "off"));
    $div->addElement($field);
    $fieldset->addElement($div);
    $field = new FieldInputSubmit();
    $field->setControlAttributes(array('value' => "导出报表",
        'class' => 'submit'));
    $fieldset->addElement($field);
    $form->addElement($fieldset);

    $form->render();
}

function admin_form()
{
    $form = new Form();

    $attributes = array(
        'class' => 'form_admin standard',
        'action' => 'report.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'export_admin_report');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend(('导出管理员报表'));

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("起始日期")
        ->setControlAttributes(array('id' => 'admin_start',
            'required' => TRUE,
            'name' => 'admin_start',
            'autocomplete' => "off"));
    $div->addElement($field);

    // The description field
    $field = new FieldInputText();
    $field->setLabel("终止日期")
        ->setControlAttributes(array('id' => 'admin_end',
            'required' => TRUE,
            'name' => 'admin_end',
            'autocomplete' => "off"));
    $div->addElement($field);
    $fieldset->addElement($div);
    $field = new FieldInputSubmit();
    $field->setControlAttributes(array('value' => "导出报表",
        'class' => 'submit'));
    $fieldset->addElement($field);
    $form->addElement($fieldset);

    $form->render();
}
if (!isset($_POST['type'])) {//
    order_form();
    massagist_form();
    admin_form();
} else if ($_POST['type'] == 'export_orders_report') {
    global $tbl_orders, $tbl_project, $tbl_massagist;
    $start_time = $_POST['export_start'];
    $end_time = $_POST['export_end'];

    if (strtotime($start_time) > strtotime($end_time)) {
        echo "<p style='color: red'> 起始时间不能大于终止时间</p>";
        exit;
    }
    $filePath .= 'orders_'.$start_time . '_' . $end_time . '.csv';
    $start_time .= " 00:00:00";
    $end_time .= " 23:59:59";
    $sql = "SELECT `id`,`abbreviation` FROM $tbl_project WHERE 1";
    $ps = db()->query($sql);
    for ($i = 0; $row = $ps->row_keyed($i); $i++) {
        $projects[$row['id']] = $row['abbreviation'];
    }
    $sql = "SELECT `id`,`name` FROM $tbl_massagist WHERE 1";
    $ms = db()->query($sql);
    for ($i = 0; $row = $ms->row_keyed($i); $i++) {
        $massagists[$row['id']] = $row['name'];
    }
    $sql = "SELECT * FROM $tbl_orders WHERE `start_timestamp` >= ? AND `start_timestamp` <= ?";
    $res = db()->query($sql, array($start_time, $end_time));
    $fields = array(
        'id' => '订单序号',
        'room_id' => '房间号',
        'start_timestamp' => '订单开始时间',
        'end_timestamp' => '订单结束时间',
        'booking_no' => '预约号',
        'booking_timestamp' => '预约时间',
        'booking_linkman' => '预约人',
        'booking_phone' => '预约电话',
        'customer_type' => '客户类型',
        'order_source' => '订单来源',
        'pay_way' => '支付方式',
        'order_amount' => '订单金额',
        'discount_amount' => '优惠金额',
        'paid_amount' => '已支付金额',
        'unpaid_amount' => '未支付金额',
        'status' => '订单状态',
        'project_id' => '项目',
        'massagist' => '技师',
        'customer_amount' => '客户数量',
        'display' => '是否删除',
    );

    $customerType = array(1 => '散客', 2 => '团购');
    $orderSource = array(1 => '大众点评', 2 => '其他');
    $payWay = array(1 => '微信(扫付款码)', 2 => '支付宝(扫付款码)'
    , 3 => '微信(客户扫码)', 4 => '支付宝(客户扫码)', 5 => '刷卡', 6 => '现金');
    for ($i = 0; $row = $res->row_keyed($i); $i++) {
        $massagistNames = array();
        $orders[$i] = array(
            'id' => $row['id'],
            'room_id' => $row['room_id'],
            'start_timestamp' => $row['start_timestamp'],
            'end_timestamp' => $row['end_timestamp'],
            'booking_no' => $row['booking_no'],
            'booking_timestamp' => $row['booking_timestamp'],
            'booking_linkman' => $row['booking_linkman'],
            'booking_phone' => $row['booking_phone'],
            'customer_type' => $customerType[(int)$row['customer_type']],
            'order_source' => $orderSource[(int)$row['order_source']],
            'pay_way' => $payWay[(int)$row['pay_way']],
            'order_amount' => (int)$row['order_amount'],
            'discount_amount' => (int)$row['discount_amount'],
            'paid_amount' => (int)$row['paid_amount'],
            'unpaid_amount' => (int)$row['unpaid_amount'],
            'status' => (int)$row['status'] == 1 ? '完成' : ((int)$row['status'] == 2 ? '正在进行' : '未进行'),
            'project' => $projects[(int)$row['project_id']],
        );


        $massagistIds = explode(',', $row['massagist']);

        if (!empty($massagistIds)) {
            foreach ($massagistIds as $id) {
                if ($id == '') {
                    $massagistNames[] = '';
                } else {
                    $massagistNames[] = $massagists[(int)$id];
                }

            }
            $orders[$i]['massagist'] = implode('、', $massagistNames);
        } else {
            $orders[$i]['massagist'] = '';

        }
        $orders[$i]['customer_amount'] = $row['customer_amount'];
        $orders[$i]['display'] = $row['display'];
    }
    if (is_file($filePath)) {
        if (unlink($filePath)) {

        } else {
            echo '已有相同的文件，请删除后重新导出';
            exit;
        }
    }
    $fp = fopen($filePath, 'w');
    $header = implode(',', array_values($fields)) . PHP_EOL;
    $content = '';
    foreach ($orders as $k => $v) {
        $content .= implode(',', $v) . PHP_EOL;
    }
    $csv = $header . $content;

    fwrite($fp, iconv('UTF-8', 'GB2312', $csv));
    fclose($fp);
    $returl = "report.php";
    header("Location: $returl");
} else if ($_POST['type'] == 'export_massagist_report') {
    global $tbl_orders, $tbl_project, $tbl_massagist, $tbl_massagist_month, $tbl_coe;
    $start_time = $_POST['massagist_start'];
    $end_time = $_POST['massagist_end'];
    if (strtotime($start_time) > strtotime($end_time)) {
        echo "<p style='color: red'> 起始时间不能大于等于终止时间</p>";
        exit;
    }

    $start_m = substr($start_time, 0, 7);
    $end_m = substr($end_time, 0, 7);
    $filePath .= 'massagists_'.$start_time . '_' . $end_time . '.csv';
    $start_time .= " 00:00:00";
    $end_time .= " 23:59:59";

    $sql = "SELECT `id`,`type`,`project_time` FROM $tbl_project WHERE 1";
    $ps = db()->query($sql);
    for ($i = 0; $row = $ps->row_keyed($i); $i++) {
        $projectsType[$row['id']] = $row['type'];
        $projectsTime[$row['id']] = $row['project_time'];
    }

    $res2 = db()->query("SELECT * FROM $tbl_massagist_month WHERE  `current_month` = ?", array($end_m));
    for ($i = 0; $row = $res2->row_keyed($i); $i++) {
        $month_info[$row['massagist_id']] = $row;
    }
    $sql = "SELECT `id`,`name`,`rank` FROM $tbl_massagist WHERE 1";
    $ms = db()->query($sql);
    for ($i = 0; $row = $ms->row_keyed($i); $i++) {
        $massagists[$row['id']] = $row;
        if (isset($month_info[$row['id']])) {
            $massagists[$row['id']]['praise'] = $month_info[$row['id']]['praise'];
            $massagists[$row['id']]['vacation'] = $month_info[$row['id']]['vacation'];
            $massagists[$row['id']]['overtime'] = $month_info[$row['id']]['overtime'];
        }
    }
    $sql = "SELECT * FROM $tbl_coe WHERE 1";
    $resCoe = db()->query($sql);
    for ($i = 0; $row = $resCoe->row_keyed($i); $i++) {
        $coeInfo[$row['type']] = $row;
    }
    $sql = "SELECT * FROM $tbl_orders WHERE `start_timestamp` >= ? AND `start_timestamp` <= ? AND`display` = 1 ";
    $resOrders = db()->query($sql, array($start_time, $end_time));
    for ($i = 0; $row = $resOrders->row_keyed($i); $i++) {
        if (empty($row['massagist'])) {
            continue;
        }
        $order_m = explode(',', $row['massagist']);
        foreach ($order_m as $m) {
            $orderMassagistInfo[$m][] = $row;
        }
    }
    $convert = array(
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
    );

    if (!empty($orderMassagistInfo)) {
        foreach ($orderMassagistInfo as $massagistId => $orders) {
            $wages = 0;
            foreach ($orders as $order) {
                $pType = (int)$projectsType[$order['project_id']];//项目的类别
                $mType =  $massagists[$massagistId]['rank'];//技师级别
                $pTime = (float)$projectsTime[$order['project_id']];//项目时长
                $wages += $coeInfo[$mType][$convert[$pType]] * $pTime ;

                if(isset($massagists[$massagistId][$convert[$pType]])){
                    $massagists[$massagistId][$convert[$pType]] += $pTime;
                }else{
                    $massagists[$massagistId][$convert[$pType]] = $pTime;
                }
            }
            $massagists[$massagistId]['wages'] = $wages;
        }
    }

    $h= array('编号','姓名','职级','好评数','假期剩余','加班时长','1类','2类','3类','4类','5类','6类','工资');
    $header = implode(',', array_values( $h)) . PHP_EOL;

    $content = '';
    foreach($massagists as $id => $m){
        $line = array() ;
        $line = array(
            'id' => $id,
            'name'=> $m['name'],
            'rank'=>$m['rank'],
            'praise'=>$m['praise'],
            'vacation'=>$m['vacation'],
            'overtime'=>$m['overtime'],
            'one'=> isset($m['one']) ? $m['one'] : '',
            'two'=> isset($m['two']) ? $m['two'] : '',
            'three'=> isset($m['three']) ? $m['three'] : '',
            'four'=> isset($m['four']) ? $m['four'] : '',
            'five'=> isset($m['five']) ? $m['five'] : '',
            'six'=> isset($m['six']) ? $m['six'] : '',
            'wages'=> isset($m['wages']) ? $m['wages'] : '',
        );
        $content .= implode(',', $line) . PHP_EOL;
    }
    $csv = $header . $content;
    if (is_file($filePath)) {
        if (unlink($filePath)) {

        } else {
            echo '已有相同的文件，请删除后重新导出';
            exit;
        }
    }
    $fp = fopen($filePath, 'w');
    fwrite($fp, iconv('UTF-8', 'GB2312', $csv));
    fclose($fp);
    $returl = "report.php";
    header("Location: $returl");

}else if ($_POST['type'] == 'export_admin_report') {
    global $tbl_orders,$tbl_admin_month, $tbl_admin, $tbl_coe;
    $start_time = $_POST['admin_start'];
    $end_time = $_POST['admin_end'];
    if (strtotime($start_time) > strtotime($end_time)) {
        echo "<p style='color: red'> 起始时间不能大于等于终止时间</p>";
        exit;
    }

    $start_m = substr($start_time, 0, 7);
    $end_m = substr($end_time, 0, 7);
    $filePath .= 'admin_'.$start_time . '_' . $end_time . '.csv';
    $start_time .= " 00:00:00";
    $end_time .= " 23:59:59";

    $res2 = db()->query("SELECT * FROM $tbl_admin_month WHERE `current_month` = ?", array($end_m));
    for ($i = 0; $row = $res2->row_keyed($i); $i++) {
        $month_info[$row['admin_id']] = $row;
    }
    $sql = "SELECT `id`,`name`,`duty` FROM $tbl_admin WHERE 1";
    $ms = db()->query($sql);
    for ($i = 0; $row = $ms->row_keyed($i); $i++) {
        $admins[$row['id']] = $row;
        if (isset($month_info[$row['id']])) {
            $admins[$row['id']]['praise'] = $month_info[$row['id']]['praise'];
            $admins[$row['id']]['vacation'] = $month_info[$row['id']]['vacation'];
            $admins[$row['id']]['overtime'] = $month_info[$row['id']]['overtime'];

        }
    }

    $h= array('编号','姓名','职级','好评数','假期剩余','加班时长');
    $header = implode(',', array_values( $h)) . PHP_EOL;

    $content = '';
    foreach($admins as $id => $m){

        $line = array(
            'id' => $id,
            'name'=> $m['name'],
            'rank'=>$m['duty'],
            'praise'=>$m['praise'],
            'vacation'=>$m['vacation'],
            'overtime'=>$m['overtime'],
        );
        $content .= implode(',', $line) . PHP_EOL;
    }
    $csv = $header . $content;
    if (is_file($filePath)) {
        if (unlink($filePath)) {

        } else {
            echo '已有相同的文件，请删除后重新导出';
            exit;
        }
    }
    $fp = fopen($filePath, 'w');
    fwrite($fp, iconv('UTF-8', 'GB2312', $csv));
    fclose($fp);
    $returl = "report.php";
    header("Location: $returl");

}