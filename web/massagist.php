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

#删除技师
function generate_massagist_delete_form($id)
{
    $form = new Form();

    $attributes = array('action' => 'del.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'delete_massagist',
        'id' => $id);
    $form->addHiddenInputs($hidden_inputs);

    // The button
    $element = new ElementInputImage();
    $element->setAttributes(array('class' => 'button',
        'src' => 'images/delete.png',
        'width' => '16',
        'height' => '16',
        'title' => get_vocab('delete'),
        'alt' => get_vocab('delete')));
    $form->addElement($element);

    $form->render();
}

#更改技师级别
function generate_edit_massagist_form()
{

    global $area;

    $form = new Form();

    $attributes = array(
        'class' => 'form_admin standard',
        'action' => 'update.php',
        'id' => 'update_massagist_rank',

        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'change_massagist_rank');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend(get_vocab('调整技师级别'));

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("编号")
        ->setControlAttributes(array('id' => 'edit_id',
            'name' => 'edit_id',
            'vaule' => '',
            "readonly" => true,
            'required' => true,
            'maxlength' => 20));
    $div->addElement($field);

    // The description field
    $field = new FieldInputText();
    $field->setLabel("姓名")
        ->setControlAttributes(array('id' => 'edit_name',
            'name' => 'edit_name',
            'vaule' => '',
            "readonly" => true,
            'maxlength' => 20));
    $div->addElement($field);
    $fieldset->addElement($div);

    $div = new ElementDiv();
    $options = array('A' => 'A',
        'B1' => 'B1',
        'B2' => 'B2',
        'B3' => 'B3',
        'C' => 'C');
    $field = new FieldSelect();
    $field->setLabel("职级")
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => 'edit_rank'));
    $div->addElement($field);
    $fieldset->addElement($div);

    // The submit button
    $field = new FieldButton();
    $field->setControlAttributes(array('type' => 'button',
        'onclick' => 'update_massagist_rank()'
    ))
        ->setControlText("更改技师职级");
    $fieldset->addElement($field);

    $form->addElement($fieldset);

    $form->render();
}

#

#更新技师状态新增技师
function generate_new_massagist_form()
{
    global $maxlength;
    global $area;

    $form = new Form();

    $attributes = array(
        'class' => 'form_admin standard',
        'action' => 'add.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'add_massagist');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend(get_vocab('添加技师'));

    $div = new ElementDiv();


    // The description field
    $field = new FieldInputText();
    $field->setLabel("姓名")
        ->setControlAttributes(array('id' => 'name',
            'name' => 'name',
            'required' => true,
            'maxlength' => 20));
    $div->addElement($field);
    $field = new FieldInputText();
    $field->setLabel("可选类别")
        ->setControlAttributes(array('id' => 'massagist_type',
            'name' => 'massagist_type',
            'required' => true,
            'maxlength' => 20));

    // Capacity

    $div->addElement($field);
    $fieldset->addElement($div);

    $div = new ElementDiv();
    $options = array('A' => 'A',
        'B1' => 'B1',
        'B2' => 'B2',
        'B3' => 'B3',
        'C' => 'C');
    $field = new FieldSelect();
    $field->setLabel("职级")
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => 'rank'));
    $div->addElement($field);

    $options = array(0 => '女', 1 => '男');
    $field = new FieldSelect();
    $field->setLabel("性别")
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => 'sex'));

    $div->addElement($field);
    $fieldset->addElement($div);

    // The submit button
    $field = new FieldInputSubmit();
    $field->setControlAttributes(array('value' => "添加技师",
        'class' => 'submit'));
    $fieldset->addElement($field);

    $form->addElement($fieldset);

    $form->render();
}

function convert_massagist_statue($id, $status)
{
    $form = new Form();
    $attributes = array('action' => 'update.php',
        'method' => 'post');
    $form->setAttributes($attributes);
    // Hidden inputs
    $hidden_inputs = array('type' => 'update_massagist_status',
        'id' => $id,
        'status' => $status);
    $form->addHiddenInputs($hidden_inputs);
    // The button
    $element = new ElementInputImage();
    $element->setAttributes(array('class' => 'button',
        'src' => 'images/repeat.png',
        'width' => '16',
        'height' => '16',
//        'title' => get_vocab('delete'),
//        'alt' => get_vocab('delete')
    ));
    $form->addElement($element);
    $form->render();
}

//增减加班时长
function add_massagist_overtime($id, $month)
{
    $form = new Form();
    $attributes = array('action' => 'update.php',
        'method' => 'post',
        'onkeydown' => "if(event.keyCode==13){return false;}"
    );
    $form->setAttributes($attributes);
    // Hidden inputs
    $hidden_inputs = array('type' => 'update_massagist_overtime',
        'id' => $id,
        'month' => $month);
    $form->addHiddenInputs($hidden_inputs);
    $element = new Element('input', 'true');
    $element->setAttributes(array('type' => 'text',
        'name' => "overtime",
        'style' => "width:100px;height:30px;",
    ));
    $form->addElement($element);
    $element = new Element('input', 'true');
    $element->setAttributes(array('type' => 'submit',
        'value' => '增加'
    ));
    $form->addElement($element);
    $form->render();
}

//增减加班时长
function add_massagist_praise($id, $month)
{
    $form = new Form();
    $attributes = array('action' => 'update.php',
        'method' => 'post',
        'onkeydown' => "if(event.keyCode==13){return false;}"
    );
    $form->setAttributes($attributes);
    // Hidden inputs
    $hidden_inputs = array('type' => 'update_massagist_praise',
        'id' => $id,
        'month' => $month);
    $form->addHiddenInputs($hidden_inputs);
    $element = new Element('input', 'true');
    $element->setAttributes(array('type' => 'text',
        'name' => "praise",
        'style' => "width:100px;height:30px;",
    ));
    $form->addElement($element);
    $element = new Element('input', 'true');
    $element->setAttributes(array('type' => 'submit',
        'value' => '增加'
    ));
    $form->addElement($element);
    $form->render();
}

//减少假期
function minus_massagist_vacation($id, $vacat, $currentDate)
{
    $form = new Form();
    $attributes = array('action' => 'update.php',
        'method' => 'post',
    );
    $form->setAttributes($attributes);
    // Hidden inputs
    $hidden_inputs = array('type' => 'minus_massagist_vacation',
        'id' => $id,
        'vacation' => $vacat,
        'month' => $currentDate);
    $form->addHiddenInputs($hidden_inputs);
    $element = new Element('input', 'true');
    $element->setAttributes(array('type' => 'submit',
        'value' => '减少'
    ));
    $form->addElement($element);
    $form->render();
}

global $tbl_massagist, $tbl_massagist_month;
// Check the CSRF token.
// Only check the token if the page is accessed via a POST request.  Therefore
// this page should not take any action, but only display data.


// Check the user is authorised for this page
checkAuthorised();

// Also need to know whether they have admin rights
$user = getUserName();
$required_level = (isset($max_level) ? $max_level : 2);
$is_admin = (authGetUserLevel($user) >= $required_level);


print_header();

// Get the details we need for this area

echo "<h1>" . "技师列表" . "</h1>\n";


// Now the custom HTML
if ($auth['allow_custom_html']) {
    echo "<div id=\"div_custom_html\">\n";
    // no htmlspecialchars() because we want the HTML!
    echo (isset($custom_html)) ? "$custom_html\n" : "";
    echo "</div>\n";
}


// BOTTOM SECTION: ROOMS IN THE SELECTED AREA
// Only display the bottom section if the user is an admin or
// else if there are some areas that can be displayed


echo "<div id=\"room_form\">\n";
$currentDate = (string)date('Y-m', time());

$res = db()->query("SELECT * FROM `$tbl_massagist` WHERE 1  ORDER BY `id`");
$monthInfo = db()->query("SELECT * FROM `$tbl_massagist_month`WHERE `current_month` = ? ORDER BY `id`", array($currentDate));
if ($res->count() == 0) {
    echo "<p>" . "没有技师" . "</p>\n";
} else {
    $str = array();
    $massagists = array();
    for ($i = 0; ($row = $res->row_keyed($i)); $i++) {
        $massagists[$row['id']] = $row;
        $massagists[$row['id']]['praise'] = 0;
        $massagists[$row['id']]['vacation'] = 4;
        $massagists[$row['id']]['overtime'] = 0.00;
        if ($monthInfo->count() == 0) {
            $str[] = '( DEFAULT,' . $row['id'] . ",'$currentDate' , 0.00, 0, 4)";
        }
    }
    if ($monthInfo->count() == 0) {
        $sql = "INSERT  INTO `$tbl_massagist_month` (id, massagist_id, current_month, overtime, praise, vacation )VALUES " . implode(',', $str);
        if (empty(db()->command($sql))) {
            fatal_error("更新数据表失败");
        }
    }
    for ($i = 0; ($row = $monthInfo->row_keyed($i)); $i++) {
        if (in_array($row['massagist_id'], array_keys($massagists))) {
            $massagists[$row['massagist_id']]['praise'] = $row['praise'];
            $massagists[$row['massagist_id']]['vacation'] = $row['vacation'];
            $massagists[$row['massagist_id']]['overtime'] = $row['overtime'];
        }
    }

    echo "<div id=\"room_info\" class=\"datatable_container\">\n";
    echo "<table id=\"rooms_table\" class=\"admin_table display\" style = \"font-size: 16px;\">\n";
    echo "<thead>\n";
    echo "<tr>\n";
    $fields = array(
        'id' => '编号', 'name' => '姓名', 'sex' => '性别', 'rank' => '职级', 'type' => '可选类别', 'overtime' => '当月加班时长',
        'vacation' => '当月休假余额', 'praise' => '当月好评数', 'status' => '状态'
    );
    foreach (array_values($fields) as $field) {

        echo "<th>$field</th>\n";
    }
    echo "<th>操作</th>\n";

    echo "</tr>\n";
    echo "</thead>\n";
    echo "<tbody>\n";
    $row_class = "odd";
    foreach ($massagists as $k => $r) {
        $row_class = ($row_class == "even") ? "odd" : "even";
        echo "<tr class=\"$row_class\">\n";
        foreach (array_keys($fields) as $field) {
            switch ($field) {
                // the standard MRBS fields
                case 'id':
                case 'name':

                case 'type':
                    echo "<td><div>" . htmlspecialchars($r[$field]) . "</div></td>\n";
                    break;
                case 'rank':
                    echo "<td><div  onclick='show_change_rank(" . (int)$r['id'] . ',"' . $r['name'] . "\")'>" . htmlspecialchars($r[$field]) . "</div></td>\n";
                    break;
                case 'status':
                    if ((int)$r['status'] == 1) {
                        $str = '忙碌';
                        $color = 'red';
                    } else if ((int)$r['status'] == 0) {
                        $str = '空闲';
                        $color = 'blue';
                    } else {
                        $str = '休假';
                        $color = '#9966CC';
                    }
                    echo "<td ><div style='float: left;color:$color'>" . htmlspecialchars($str)
                        . '</div><div style=\'float: left\'>';
                    convert_massagist_statue((int)$r['id'], (int)$r['status']);
                    echo "</div></div></td>\n";
                    break;
                case 'overtime':
                    echo "<td ><div style='float: left;'>" . htmlspecialchars($r[$field])
                        . '</div><div style=\'float: left\'>';
                    add_massagist_overtime((int)$r['id'], $currentDate);
                    echo "</div></div></td>\n";
                    break;
                case 'vacation':
                    echo "<td ><div style='float: left;'>" . htmlspecialchars($r[$field])
                        . '</div><div style=\'float: left\'>';
                    minus_massagist_vacation((int)$r['id'], (int)$r['vacation'], $currentDate);
                    echo "</div></div></td>\n";
                    break;
                case 'praise':
                    echo "<td ><div style='float: left;'>" . htmlspecialchars($r[$field])
                        . '</div><div style=\'float: left\'>';
                    add_massagist_praise((int)$r['id'], $currentDate);
                    echo "</div></div></td>\n";
                    break;
                case 'sex':
                    echo "<td ><div>" . htmlspecialchars((int)$r['sex'] == 0 ? '女' : '男') . "</div></td>\n";
                    break;
                // any user defined fields

            }  // switch
        }  // foreach


        echo "<td>\n<div>\n";
        generate_massagist_delete_form((int)$r['id']);
        echo "</div>\n</td>\n";


        echo "</tr>\n";

    }

    echo "</tbody>\n";
    echo "</table>\n";
    echo "</div>\n";


}

generate_new_massagist_form();


echo "</div>\n";
generate_edit_massagist_form();