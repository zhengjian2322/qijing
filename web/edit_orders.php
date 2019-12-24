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
use MRBS\Form\FieldInputCheckbox;
use MRBS\Form\FieldInputCheckboxGroup;
use MRBS\Form\FieldInputDatalist;
use MRBS\Form\FieldInputDate;
use MRBS\Form\FieldInputNumber;
use MRBS\Form\FieldInputRadioGroup;
use MRBS\Form\FieldInputSubmit;
use MRBS\Form\FieldInputText;
use MRBS\Form\FieldSelect;


require "defaultincludes.inc";
require_once "mrbs_sql.inc";

function get_field_name($name, $maxlength, $label, $required = TRUE)
{
    $field = new FieldInputText();
    $field->setLabel($label)
        ->setControlAttributes(array('id' => $name,
            'name' => $name,
            'required' => $required,
            'maxlength' => $maxlength));
    return $field;
}
function get_field_name_disabled($label, $value = '')
{
    $field = new FieldInputText();
    $field->setLabel($label)
        ->setControlAttributes(array(
            'name' => 'booking_no',
            'readonly' => true,
            'value' => $value));
    return $field;
}

function get_field_booking_select($options, $key, $label = null, $area)
{
    $field = new FieldSelect();
    $field->setLabel($label)
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => $key));
    return $field;


}

function get_field_booking_payment($name, $maxlength, $label, $required = TRUE)
{
    $field = new FieldInputText();
    $field->setLabel($label)
        ->setControlAttributes(array('id' => $name,
            'name' => $name,
            'required' => $required,
            'maxlength' => $maxlength));
    return $field;
}


// Generate a time or period selector starting with $first and ending with $last.
// $time is a full Unix timestamp and is the current value.  The selector returns
// the start time in seconds since the beginning of the day for the start of that slot.
// Note that these are nominal seconds and do not take account of any DST changes that
// may have happened earlier in the day.  (It's this way because we don't know what day
// it is as that's controlled by the date selector - and we can't assume that we have
// JavaScript enabled to go and read it)
//
//    $display_none parameter     sets the display style of the <select> to "none"
//    $disabled parameter         disables the input and also generate a hidden input, provided
//                                that $display_none is FALSE.  (This prevents multiple inputs
//                                of the same name)
//    $is_start                   Boolean.  Whether this is the start selector.  Default FALSE
function get_slot_selector($id, $name, $current_s  )
{
    global $morningstarts,$morningstarts_minutes,$eveningends, $eveningends_minutes;
    $options = array();
    // If we're using periods then the last slot is actually the start of the last period,
    // or if we're using times and this is the start selector, then we don't show the last
    // time
    $f = ($morningstarts * 60 + $morningstarts_minutes) * 60;
    $last = ((($eveningends * 60) + $eveningends_minutes) * 60)  ;
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

    $back = new ElementInputSubmit();
    $back->setAttributes(array('name' => 'back_button',
        'value' => get_vocab('back'),
        'formnovalidate' => true));

    $submit = new ElementInputSubmit();
    $submit->setAttributes(array('class' => 'default_action',
        'name' => 'save_button',
        'value' => "保存"));

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


// Returns the booking date for a given time.   If the booking day spans midnight and
// $t is in the interval between midnight and the end of the day then the booking date
// is really the day before.
//
// If $is_end is set then this is the end time and so if the booking day happens to
// last exactly 24 hours, when there will be two possible answers, we want the later
// one.
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
function get_field_massagist_select($options, $key, $label = null, $area)
{
    $field = new FieldSelect();

    $field->setLabel($label)
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array(
            'name' => $key,
            'class' => "chosen-select",
            'multiple' => true,
            'data-placeholder' => "请选择技师"
        ));
    return $field;
}

global $tbl_rooms,$tbl_project,$tbl_massagist,$tbl_orders;
// Get non-standard form variables
$hour = get_form_var('hour', 'int');
$minute = get_form_var('minute', 'int');
$returl = get_form_var('returl', 'string');




// Get the return URL.  Need to do this before checkAuthorised().
// We might be going through edit_entry more than once, for example if we have to log on on the way.  We
// still need to preserve the original calling page so that once we've completed edit_entry_handler we can
// go back to the page we started at (rather than going to the default view).  If this is the first time
// through, then $HTTP_REFERER holds the original caller.    If this is the second time through we will have
// stored it in $returl.
if (!isset($returl)) {
    $returl = isset($HTTP_REFERER) ? $HTTP_REFERER : "";
}


// Also need to know whether they have admin rights
$user = getUserName();
$room = get_form_var('room','int');

if(!isset($room) || empty($room)){
    fatal_error("请从首页进入");
}

print_header($day, $month, $year, $area, isset($room) ? $room : null);

$start_time = mktime($hour, $minute, 0, $month, $day, $year);

// If the start time is not on a slot boundary, then make it so.  (It's just possible that it won't be
// if (a) somebody messes with the query string or (b) somebody changes morningstarts or the
// resolution in another browser window and then this page is refreshed with the same query string).

$st = "$year-$month-$day 00:00:00";
$et = "$year-$month-".((int)$day +1)." 00:00:00";
$res = db()->query1("SELECT COUNT(`id`) FROM $tbl_orders WHERE `start_timestamp` > '$st' AND `start_timestamp` < '$et'");
if($res == -1){
    $res = 0;
}
$booking_no = "$year$month$day" .sprintf("%03d",$res);;

$form = new Form();

$form->setAttributes(array('class' => 'standard',
    'id' => 'main',
    'action' => 'edit_orders_handler.php',
    'method' => 'post'));

$form->addHiddenInputs(array('returl' => $returl,
    'room_id' => $room));


$fieldset = new ElementFieldset();

$edit_order_field_order = ['booking_no','project_id','massagist','booking_linkman','customer_amount','booking_phone','booking_timestamp','start_timestamp','end_timestamp'
    , 'customer_type', 'order_source','pay_way','order_amount', 'discount_amount','paid_amount','unpaid_amount'];

$res = db()->query1("SELECT `type` FROM $tbl_rooms WHERE `id`= ? " ,array((int)$room));
$projects = db()->query("SELECT `id`,`abbreviation` FROM $tbl_project WHERE `type` IN ( $res )"  );

$projectsOption = array();
if($projects->count() == 0){
    $projectsOption[0] = "无项目可选";
}else{
    for ($i = 0; ($row = $projects->row_keyed($i)); $i++) {
        $projectsOption[$row['id']] = $row['abbreviation'];
    }
}
$massagists = db()->query("SELECT `id`,`name` FROM $tbl_massagist WHERE `status` = 0 " );
if($massagists->count() == 0){
    $massagistsOption[0] = "无空闲技师可选";
}else{
    for ($i = 0; ($row = $massagists->row_keyed($i)); $i++) {
        $massagistsOption[$row['id']] = $row['name'];
    }
}

foreach ($edit_order_field_order as $key) {
    switch ($key) {
        case 'booking_no':
            $fieldset->addElement(get_field_name_disabled('预约号', $booking_no));
            break;
        case 'project_id':
            $fieldset->addElement(get_field_booking_select( $projectsOption, $key, '项目', 0));
            break;
//        case 'massagist':
//            $fieldset->addElement(get_field_massagist_select( $massagistsOption, "massagist[]", '技师选择', 0));
//            break;
        case 'booking_linkman':
            $fieldset->addElement(get_field_name($key, 20, '预约人', TRUE));
            break;
        case 'customer_amount':
            $fieldset->addElement(get_field_name($key, 20, '预约人数', TRUE));
            break;
        case 'booking_phone':
            $fieldset->addElement(get_field_name($key, 11, '手机号', TRUE));
            break;
        case 'booking_timestamp':
            $fieldset->addElement(get_field_booking_time($start_time));
            break;

        case 'start_timestamp':
            $fieldset->addElement(get_field_start_time($start_time));
            break;

        case 'end_timestamp':
            $fieldset->addElement(get_field_end_time($start_time));
            break;
        case 'customer_type':
            $options = array(1 => '散客', 2 => '团购');
            $fieldset->addElement(get_field_booking_select($options, $key, '客户类型', 0));
            break;
        case 'order_source':
            $options = array(1 => '大众点评', 2 => '其他');
            $fieldset->addElement(get_field_booking_select($options, $key, '客户来源', 0));
            break;
        case 'pay_way':
            $options = array(1 => '微信(扫付款码)', 2 => '支付宝(扫付款码)'
            , 3 => '微信(客户扫码)', 4 => '支付宝(客户扫码)', 5 => '刷卡', 6 => '现金');
            $fieldset->addElement(get_field_booking_select($options, $key, '支付方式', 0));
            break;
        case 'order_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '订单金额', false));
            break;
        case 'discount_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '优惠金额', false));
            break;
        case 'paid_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '已支付金额', false));
            break;
        case 'unpaid_amount':
            $fieldset->addElement(get_field_booking_payment($key, 11, '未支付金额', false));
            break;


    } // switch
} // foreach

$form->addElement($fieldset);


$form->addElement(get_fieldset_submit_buttons());

$form->render();

//$returl = "admin.php?area=$area" . (!empty($error) ? "&error=$error" : "");
//header("Location: $returl");