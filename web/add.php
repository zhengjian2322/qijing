<?php

namespace MRBS;

use MRBS\Form\Form;

require "defaultincludes.inc";
require_once "mrbs_sql.inc";




// Check the user is authorised for this page
checkAuthorised();

// Get non-standard form variables

$type = get_form_var('type', 'string', null, INPUT_POST);
function checkInt($num)
{

    if (!is_numeric($num)) {
        return false;
    }else{
        if(strpos($num, '.')){
            return false;
        }
    }
    return true;
}

if ($type == "add_massagist") {

    $name = get_form_var('name', 'string', null, INPUT_POST);
    $sex = get_form_var('sex', 'int', null, INPUT_POST);
    $rank = get_form_var('rank', 'string', null, INPUT_POST);
    $type = get_form_var('massagist_type', 'string', null, INPUT_POST);
    if (!isset($name) || ($name === '')) {
        fatal_error("姓名不能为空");
    }

    if (!isset($sex) || ($sex === '')) {
        fatal_error("性别不能为空");
    }
    if (!isset($rank) || ($rank === '')) {
        fatal_error("职级不能为空");
    }
    if (!isset($type) || ($type === '')) {
        fatal_error("可选类别不能为空");
    }
    $massagist = array(
        'name' => $name,
        'sex' => $sex,
        'rank' => $rank,
        'type' => "$type",
    );
    $room = mrbsAddMassagist($massagist);
    if (empty($room)) {
        fatal_error("添加技师错误，请重新添加！");
    }
    $returl = "massagist.php";
} else if ($type == "add_admin") {
    $name = get_form_var('name', 'string', null, INPUT_POST);
    $sex = get_form_var('sex', 'int', null, INPUT_POST);
    $duty = get_form_var('duty', 'string', null, INPUT_POST);
    if (!isset($name) || ($name === '')) {
        fatal_error("姓名不能为空");
    }

    if (!isset($sex) || ($sex === '')) {
        fatal_error("性别不能为空");
    }
    if (!isset($duty) || ($duty === '')) {
        fatal_error("职务不能为空");
    }
    $admin = array(
        'name' => $name,

        'sex' => $sex,
        'duty' => $duty
    );
    $r = mrbsAddAdmin($admin);
    if (empty($r)) {
        fatal_error("添加技师错误，请重新添加！");
    }
    $returl = "admins.php";

} else if ($type == "add_project") {
    $number = $_POST['number'];
    $name = get_form_var('name', 'string', null, INPUT_POST);
    $abbr = get_form_var('abbreviation', 'string', null, INPUT_POST);
    $type =  $_POST['project_type'];
    $projectTime = $_POST['project_time'];
    $projectPrice = $_POST['project_price'];
    if (!isset($number) || ($number === '') || !checkInt($number)) {
        fatal_error("编号必须为整数");
    }
    if (!isset($name) || ($name === '')) {
        fatal_error("项目名称不能为空");
    }
    if (!isset($abbr) || ($abbr === '')) {
        fatal_error("项目简称不能为空");
    }
    if (!isset( $type) || ( $type === '')) {
        fatal_error("项目分类不能为空");
    }
    if (!isset($projectTime) || ($projectTime === '') || !checkInt($projectTime)) {
        fatal_error("项目时长必须为整数");
    }
    if (!isset( $projectPrice) || ( $projectPrice === '') || !checkInt( $projectPrice)) {
        fatal_error("项目价格必须为整数");
    }

    $projects = array(
        'number' => $number,
        'name' => $name,
        'abbreviation'=> $abbr,
        'project_type' => $type,
        'project_time'=> $projectTime,
        'project_price'=> $projectPrice
    );
    $r = mrbsAddProject( $projects);
    if (empty($r)) {
        fatal_error("添加技师错误，请重新添加！");
    }
    $returl = "project.php";
}
header("Location: $returl");


