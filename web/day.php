<?php

namespace MRBS;

use MRBS\Form\Form;
use MRBS\Form\ElementDiv;
use MRBS\Form\ElementFieldset;
use MRBS\Form\ElementInputCheckbox;
use MRBS\Form\ElementInputDate;
use MRBS\Form\ElementInputHidden;
use MRBS\Form\ElementInputRadio;
use MRBS\Form\ElementInputSubmit;
use MRBS\Form\ElementLabel;
use MRBS\Form\ElementSelect;
use MRBS\Form\ElementSpan;
use MRBS\Form\FieldDiv;
use MRBS\Form\FieldButton;
use MRBS\Form\FieldInputSubmit;
use MRBS\Form\FieldInputText;
use MRBS\Form\FieldSelect;

require "defaultincludes.inc";
require_once "mincals.inc";
require_once "functions_table.inc";

// Get non-standard form variables
$timetohighlight = get_form_var('timetohighlight', 'int');


// Check the user is authorised for this page
checkAuthorised();

$inner_html = day_table_innerhtml($day, $month, $year, $room, $timetohighlight);



// Form the room parameter for use in query strings.    We want to preserve room information
// if possible when switching between views
$room_param = (empty($room)) ? "" : "&amp;room=$room";

$timestamp = mktime(12, 0, 0, $month, $day, $year);

// print the page header
print_header($day, $month, $year, $area, isset($room) ? $room : null);

echo "<div id=\"dwm_header\" class=\"screenonly\">\n";

// Draw the three month calendars
if (!$display_calendar_bottom) {
    minicals($year, $month, $day, $area, $room, 'day');
}

echo "</div>\n";


//y? are year, month and day of yesterday
//t? are year, month and day of tomorrow

// find the last non-hidden day
$d = $day;
do {
    $d--;
    $i = mktime(12, 0, 0, $month, $d, $year);
} while (is_hidden_day(date("w", $i)) && ($d > $day - 7));  // break the loop if all days are hidden
$yy = date("Y", $i);
$ym = date("m", $i);
$yd = date("d", $i);

// find the next non-hidden day
$d = $day;
do {
    $d++;
    $i = mktime(12, 0, 0, $month, $d, $year);
} while (is_hidden_day(date("w", $i)) && ($d < $day + 7));  // break the loop if all days are hidden
$ty = date("Y", $i);
$tm = date("m", $i);
$td = date("d", $i);


// Show current date and timezone
echo "<div id=\"dwm\">\n";
echo "<h2>" . utf8_strftime($strftime_format['date'], $timestamp) . "</h2>\n";
if ($display_timezone) {
    echo "<div class=\"timezone\">";
    echo get_vocab("timezone") . ": " . date('T', $timestamp) . " (UTC" . date('O', $timestamp) . ")";
    echo "</div>\n";
}
echo "</div>\n";

// Generate Go to day before and after links
$href_before = "day.php?area=$area$room_param&amp;year=$yy&amp;month=$ym&amp;day=$yd";
$href_now = "day.php?area=$area$room_param";
$href_after = "day.php?area=$area$room_param&amp;year=$ty&amp;month=$tm&amp;day=$td";

$before_after_links_html = "
<nav class=\"date_nav\">
  <a class=\"date_before\" href=\"$href_before\">" . get_vocab("daybefore") . "</a>
  <a class=\"date_now\" href=\"$href_now\">" . get_vocab("gototoday") . "</a>
  <a class=\"date_after\" href=\"$href_after\">" . get_vocab("dayafter") . "</a>
</nav>\n";

global $tbl_orders, $tbl_rooms, $tbl_project, $tbl_massagist;

function getbookingdate($t, $is_end = false)
{
    global $eveningends, $eveningends_minutes, $resolution;

    $date = getdate($t);

    $t_secs = (($date['hours'] * 60) + $date['minutes']) * 60;
    $e_secs = (((($eveningends * 60) + $eveningends_minutes) * 60) + $resolution) % SECONDS_PER_DAY;

    if (day_past_midnight()) {
        if (($t_secs < $e_secs) ||
            (($t_secs == $e_secs) && $is_end)) {
            $date = getdate(mktime($date['hours'], $date['minutes'], $date['seconds'],
                $date['mon'], $date['mday'] - 1, $date['year']));
            $date['hours'] += 24;
        }
    }

    return $date;
}

function get_slot_selector($id, $name, $current_s)
{
    global $morningstarts, $morningstarts_minutes, $eveningends, $eveningends_minutes;
    $options = array();
    // If we're using periods then the last slot is actually the start of the last period,
    // or if we're using times and this is the start selector, then we don't show the last
    // time
    $f = ($morningstarts * 60 + $morningstarts_minutes) * 60;
    $last = ((($eveningends * 60) + $eveningends_minutes) * 60);
    // If the end of the day is the same as or before the start time, then it's really on the next day
    if ($f >= $last) {
        $last += SECONDS_PER_DAY;
    }
    for ($s = $f; $s <= $last; $s += 600) {

        $options[$s] = hour_min($s);
    }

    $field = new ElementSelect();
    $field->setAttributes(array('id' => $id,
        'name' => $name))
        ->addSelectOptions($options, $current_s, true);
    return $field;
}

function get_field_name($name, $maxlength, $label, $required = TRUE, $value = '')
{

    $field = new FieldInputText();
    $field->setLabel($label)
        ->setControlAttributes(array('id' => $name,
            'name' => $name,
            'value' => $value,
            'required' => $required,
            'maxlength' => $maxlength));
    return $field;
}

function get_field_name_disabled($label, $value = '',$name)
{
    $field = new FieldInputText();
    $field->setLabel($label)
        ->setControlAttributes(array(
            'name'=>$name,
            'readonly' => true,
            'value' => $value));
    return $field;
}

function get_field_booking_select($options, $key, $label = null, $area, $value)
{
    $field = new FieldSelect();
    if (!empty($value)) {
        $area = $value;
    }
    $field->setLabel($label)
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => $key));
    return $field;
}


function get_field_massagist_select($options, $key, $label = null, $area)
{
    $field = new FieldSelect();
    $field->setLabel($label)
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('id' => 'update_order_massagist',
            'name' => $key,
            'class' => "chosen-select",
            'multiple' => true,
            'data-placeholder' => "请选择技师"
        ));
    return $field;
}

function get_field_booking_payment($name, $maxlength, $label, $required = TRUE, $value)
{
    $field = new FieldInputText();
    $field->setLabel($label)
        ->setControlAttributes(array('id' => $name,
            'name' => $name,
            'value' => $value,
            'required' => $required,
            'maxlength' => $maxlength));
    return $field;
}


function get_field_booking_time($value, $disabled = false)
{
    $date = getbookingdate($value);
    $start_date = format_iso_date($date['year'], $date['mon'], $date['mday']);
    $current_s = (($date['hours'] * 60) + $date['minutes']) * 60;

    $field = new FieldDiv();

    // Generate the live slot selector and all day checkbox
    $element_date = new ElementInputDate();
    $element_date->setAttributes(array('id' => 'booking_date',
        'name' => 'booking_date',
        'value' => $start_date,
        'disabled' => $disabled,
        'required' => true));

    $field->setLabel('预约时间')
        ->addControlElement($element_date)
        ->addControlElement(get_slot_selector(
            'booking_seconds',
            'booking_seconds',
            $current_s));

    return $field;
}

function get_field_end_time($value, $disabled = false)
{
    $date = getbookingdate($value);
    $start_date = format_iso_date($date['year'], $date['mon'], $date['mday']);
    $current_s = (($date['hours'] * 60) + $date['minutes']) * 60;

    $field = new FieldDiv();

    // Generate the live slot selector and all day checkbox
    $element_date = new ElementInputDate();
    $element_date->setAttributes(array('id' => 'end_date',
        'name' => 'end_date',
        'value' => $start_date,
        'disabled' => $disabled,
        'required' => true));

    $field->setLabel('结束时间')
        ->addControlElement($element_date)
        ->addControlElement(get_slot_selector(
            'end_seconds',
            'end_seconds',
            $current_s));

    return $field;
}

function get_field_start_time($value, $disabled = false)
{
    $date = getbookingdate($value);
    $start_date = format_iso_date($date['year'], $date['mon'], $date['mday']);
    $current_s = (($date['hours'] * 60) + $date['minutes']) * 60;

    $field = new FieldDiv();

    // Generate the live slot selector and all day checkbox
    $element_date = new ElementInputDate();
    $element_date->setAttributes(array('id' => 'start_date',
        'name' => 'start_date',
        'value' => $start_date,
        'disabled' => $disabled,
        'required' => true));

    $field->setLabel("起始时间")
        ->addControlElement($element_date)
        ->addControlElement(get_slot_selector(
            'start_seconds',
            'start_seconds',
            $current_s));
    return $field;
}

function get_fieldset_submit_buttons()
{
    $fieldset = new ElementFieldset();

    // The back and submit buttons
    $field = new FieldDiv();

    $submit = new ElementInputSubmit();
    $submit->setAttributes(array('class' => 'default_action',
        'name' => 'save_button',
        'value' => get_vocab('save')));

    // div to hold the results of the Ajax checking of the booking
    $div = new ElementDiv();
    $span_conflict = new ElementSpan();
    $span_conflict->setAttribute('id', 'conflict_check');
    $span_policy = new ElementSpan();
    $span_policy->setAttribute('id', 'policy_check');
    $div->setAttribute('id', 'checks')
        ->addElement($span_conflict)
        ->addElement($span_policy);

    $field->setAttribute('class', 'submit_buttons')
        ->addLabelClass('no_suffix')
        ->addLabelElement($back)
        ->addControlElement($submit)
        ->addControlElement($div);

    $fieldset->addElement($field);


    return $fieldset;
}

// and output them
echo $before_after_links_html;
echo "<div style=' width: 65%;float: left'>";
echo "<table class=\"dwm_main\" id=\"day_main\" data-resolution=\"$resolution\">\n";
echo $inner_html;
echo "</table>\n";
echo "</div><div style='float: left'>";

$edit_order_field_order = ['booking_no', 'project_id', 'massagist', 'booking_linkman', 'booking_phone', 'customer_amount' , 'booking_timestamp', 'start_timestamp', 'end_timestamp'
    , 'customer_type', 'order_source', 'pay_way', 'order_amount', 'discount_amount', 'paid_amount', 'unpaid_amount'];

$form = new Form();
$form->setAttributes(array('class' => 'standard',
    'id' => 'main',
    'action' => 'update.php',
    'method' => 'post'));


$fieldset = new ElementFieldset();
$id = get_form_var('id', 'int', null);

$value = array(
    'booking_no' => '',
    'booking_linkman' => '',
    'booking_phone' => '',
    'booking_timestamp' => '',
    'start_timestamp' => '',
    'end_timestamp' => '',
    'customer_type' => 1,
    'order_source' => 1,
    'pay_way' => 1,
    'order_amount' => 0,
    'discount_amount' => 0,
    'paid_amount' => 0,
    'unpaid_amount' => 0,
    'project_id' => 0,
    'massagist' => null,
    'customer_amount' => 1
);
$project = array(
    'abbreviation' => ''
);
if (isset($id) && !empty($id)) {
    $res = db()->query("SELECT *  FROM $tbl_orders WHERE `id` = ? LIMIT 1", array((int)$id));
    $order = $res->all_rows_keyed();
    foreach ($order[0] as $k => $v) {
        $value [$k] = $v;
    }
    $room = $order[0]['room_id'];
    $form->addHiddenInputs(array(
        'room_id' => $room));

    $res1 = db()->query("SELECT `type`,`abbreviation` FROM $tbl_project WHERE `id` = ? ", array($value['project_id']));
    $project = $res1->row_keyed(0) ;

    $freeMs = db()->query("SELECT *  FROM $tbl_massagist WHERE `status` = 0 ");
   if( $freeMs->count() != 0){
       $massagistTypes = array();
       for ($i = 0; ($row = $freeMs->row_keyed($i)); $i++) {
           $massagists[$row['id']] = $row['name'];
           $types = explode(',',$row['type']);
          if(in_array($project['type'], $types)){
               $massagistTypes[$row['id']] = $row['name'];
           }
       }
    }


}



//$res = db()->query1("SELECT `type` FROM $tbl_rooms WHERE `id`= ? ", array((int)$room));

//$projectsOption = array();
//if ($projects->count() == 0) {
//    $projectsOption[0] = "无项目可选";
//} else {
//    for ($i = 0; ($row = $projects->row_keyed($i)); $i++) {
//        $projectsOption[$row['id']] = $row['abbreviation'];
//    }
//
//}



foreach ($edit_order_field_order as $key) {
    switch ($key) {
        case 'booking_no':
            $fieldset->addElement(get_field_name_disabled('预约号', $value[$key],'booking_no'));
            break;
        case 'project_id':
            $fieldset->addElement(get_field_name_disabled('项目', $project['abbreviation'], $key));
            break;
        case 'massagist':
            if (empty($value[$key])) {
                    if (empty($massagistTypes)) {
                        $massagistTypes[0] = "无空闲技师可选";
                    }
                    $fieldset->addElement(get_field_massagist_select($massagistTypes, "massagist[]", '技师选择', 0));
            } else {
                $massagists = db()->query("SELECT `name` FROM $tbl_massagist WHERE `id` IN ($value[$key])");
                for ($i = 0; ($row = $massagists->row_keyed($i)); $i++) {
                    $massagistsOption[] = $row['name'];
                }
                $fieldset->addElement(get_field_name_disabled('技师选择', implode(',', $massagistsOption), 'massagist'));
            }
            break;

        case 'booking_linkman':
            $fieldset->addElement(get_field_name_disabled('预约人', $value[$key],$key ));
            break;
        case 'booking_phone':
            $fieldset->addElement(get_field_name_disabled('手机号',   $value[$key], $key ));
            break;
        case 'customer_amount':
            $fieldset->addElement(get_field_name($key, 4, '预约人数', TRUE, $value[$key]));
            break;
        case 'booking_timestamp':
            $fieldset->addElement(get_field_booking_time(strtotime($value[$key])));
            break;

        case 'start_timestamp':
            $fieldset->addElement(get_field_start_time(strtotime($value[$key])));
            break;

        case 'end_timestamp':
            $fieldset->addElement(get_field_end_time(strtotime($value[$key])));
            break;
        case 'customer_type':
            $options = array(1 => '散客', 2 => '团购');
            $fieldset->addElement(get_field_booking_select($options, $key, '客户类型', $value[$key], $value[$key]));
            break;
        case 'order_source':
            $options = array(1 => '大众点评', 2 => '其他');
            $fieldset->addElement(get_field_booking_select($options, $key, '客户来源', $value[$key], $value[$key]));
            break;
        case 'pay_way':
            $options = array(1 => '微信(扫付款码)', 2 => '支付宝(扫付款码)'
            , 3 => '微信(客户扫码)', 4 => '支付宝(客户扫码)', 5 => '刷卡', 6 => '现金');
            $fieldset->addElement(get_field_booking_select($options, $key, '支付方式', $value[$key], $value[$key]));
            break;
        case 'order_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '订单金额', false, $value[$key]));
            break;
        case 'discount_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '优惠金额', false, $value[$key]));
            break;
        case 'paid_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '已支付金额', false, $value[$key]));
            break;
        case 'unpaid_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '未支付金额', false, $value[$key]));
            break;


    } // switch
} // foreach

$form->addElement($fieldset);
if (isset($id) && !empty($id)) {
    $form->addHiddenInputs(array(
        'order_id' => $id,
        'type' => 'update_order'));
    $field = new FieldInputSubmit();
    $field->setControlAttributes(array('value' => "更新订单信息",
        'class' => 'submit'));
    $form->addElement($field);
    $form->render();

    $form = new Form();
    $form->setAttributes(array(
        'id' => 'inv_order',
        'action' => 'update.php',
        'method' => 'post'));
    $form->addHiddenInputs(array(
        'order_id' => $id,
        'type' => 'invisible_order'));
    $field = new FieldButton();
    $field->setControlAttributes(array('type' => 'button' ,
        'onclick' => 'invisible_order()'
    ))   ->setControlText("删除订单信息");
    $form->addElement($field);
    $field = new FieldButton();
    $field->setControlAttributes(array('type' => 'button' ,
        'onclick' => 'send_message()'
    ))   ->setControlText("发送短信息");
    $form->addElement($field);
    $form->render();
}else{
    $form->render();
}




echo "</div>";
echo $before_after_links_html;

show_colour_key();

output_trailer();

