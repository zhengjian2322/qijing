<?php

namespace MRBS;

use MRBS\Form\ElementDiv;
use MRBS\Form\Form;
use MRBS\Form\FieldButton;
use MRBS\Form\ElementFieldset;
use MRBS\Form\ElementInputImage;
use MRBS\Form\Element;
use MRBS\Form\FieldInputText;
use MRBS\Form\FieldInputSubmit;
use MRBS\Form\FieldSelect;

require "defaultincludes.inc";

#删除技师
function generate_admin_delete_form($id)
{
    $form = new Form();

    $attributes = array('action' => 'del.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'delete_admin',
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

function generate_edit_admin_form()
{

    global $area;

    $form = new Form();

    $attributes = array(
        'class' => 'form_admin standard',
        'id' => 'chang_admin_role',
        'action' => 'update.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'change_admin_role');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend(get_vocab('更改管理员角色'));

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("编号")
        ->setControlAttributes(array('id' => 'edit_id',
            'name' => 'edit_id',
            "readonly" => true,
            'maxlength' => 20));
    $div->addElement($field);

    // The description field
    $field = new FieldInputText();
    $field->setLabel("姓名")
        ->setControlAttributes(array('id' => 'edit_name',
            'name' => 'edit_name',
            "readonly" => true,
            'maxlength' => 20));
    $div->addElement($field);
    $fieldset->addElement($div);

    $div = new ElementDiv();

    $options = array('店长' => '店长',
        '管家' => '管家',
        '高级管家' => '高级管家');
    $field = new FieldSelect();
    $field->setLabel("职务")
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => 'duty'));
    $div->addElement($field);
    $fieldset->addElement($div);


    // The submit button
    $field = new FieldButton();
    $field->setControlAttributes(array('type' => 'button' ,
        'onclick' => 'chang_admin_role()'
    ))
        ->setControlText("更改管理员角色");
    $fieldset->addElement($field);

    $form->addElement($fieldset);

    $form->render();
}

#新增管理员
function generate_new_admin_form()
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
    $hidden_inputs = array('type' => 'add_admin');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend(get_vocab('添加管理员'));

    $div = new ElementDiv();


    // The description field
    $field = new FieldInputText();
    $field->setLabel("姓名")
        ->setControlAttributes(array('id' => 'name',
            'name' => 'name',
            'required' => true,
            'maxlength' => 20));
    $div->addElement($field);

    // Capacity
    $options = array(0 => '女', 1 => '男');
    $field = new FieldSelect();
    $field->setLabel("性别")
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => 'sex'));
    $div->addElement($field);

    $options = array('店长' => '店长',
        '管家' => '管家',
        '高级管家' => '高级管家');
    $field = new FieldSelect();
    $field->setLabel("职务")
        ->addSelectOptions($options, $area, true)
        ->setControlAttributes(array('name' => 'duty'));
    $div->addElement($field);
    $fieldset->addElement($div);


    // The submit button
    $field = new FieldInputSubmit();
    $field->setControlAttributes(array('value' => "添加管理员",
        'class' => 'submit'));
    $fieldset->addElement($field);

    $form->addElement($fieldset);

    $form->render();
}


//增减加班时长
function add_admin_overtime($id, $month)
{
    $form = new Form();
    $attributes = array('action' => 'update.php',
        'method' => 'post',
        'onkeydown' => "if(event.keyCode==13){return false;}"
    );
    $form->setAttributes($attributes);
    // Hidden inputs
    $hidden_inputs = array('type' => 'update_admin_overtime',
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
function add_admin_praise($id, $month)
{
    $form = new Form();
    $attributes = array('action' => 'update.php',
        'method' => 'post',
        'onkeydown' => "if(event.keyCode==13){return false;}"
    );
    $form->setAttributes($attributes);
    // Hidden inputs
    $hidden_inputs = array('type' => 'update_admin_praise',
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
function minus_admin_vacation($id, $vacat, $currentDate)
{
    $form = new Form();
    $attributes = array('action' => 'update.php',
        'method' => 'post',
    );
    $form->setAttributes($attributes);
    // Hidden inputs
    $hidden_inputs = array('type' => 'minus_admin_vacation',
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

global $tbl_admin, $tbl_admin_month;
// Check the CSRF token.
// Only check the token if the page is accessed via a POST request.  Therefore
// this page should not take any action, but only display data.


// Check the user is authorised for this page
checkAuthorised();

// Also need to know whether they have admin rights
$user = getUserName();
$required_level = (isset($max_level) ? $max_level : 2);
$is_admin = (authGetUserLevel($user) >= $required_level);

// Get non-standard form variables
$error = get_form_var('error', 'string');

print_header();

// Get the details we need for this area

echo "<h2>" . "管理员列表" . "</h2>\n";


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
if (isset($area)) {
    $res = db()->query("SELECT * FROM `$tbl_admin` WHERE 1  ORDER BY `id`");
    $monthInfo = db()->query("SELECT * FROM `$tbl_admin_month`WHERE `current_month` = ? ORDER BY `id`", array($currentDate));
    if ($res->count() == 0) {
        echo "<p>" . "没有管理员" . "</p>\n";
    } else {
        $str = array();
        $admins = array();
        for ($i = 0; ($row = $res->row_keyed($i)); $i++) {
            $admins[$row['id']] = $row;
            $admins[$row['id']]['praise'] = 0;
            $admins[$row['id']]['vacation'] = 4;
            $admins[$row['id']]['overtime'] = 0.00;
            if ($monthInfo->count() == 0) {
                $str[] = '( DEFAULT,' . $row['id'] . ",'$currentDate' , 0.00, 0, 4)";
            }
        }
        if ($monthInfo->count() == 0) {
            $sql = "INSERT  INTO `$tbl_admin_month` (id, admin_id, current_month, overtime, praise, vacation )VALUES " . implode(',', $str);
            if (empty(db()->command($sql))) {
                fatal_error("更新数据表失败");
            }
            $monthInfo = db()->query("SELECT * FROM `$tbl_admin_month`WHERE `current_month` = ? ORDER BY `id`", array($currentDate));
        }
        for ($i = 0; ($row = $monthInfo->row_keyed($i)); $i++) {
            if (in_array($row['admin_id'], array_keys($admins))) {
                $admins[$row['admin_id']]['praise'] = $row['praise'];
                $admins[$row['admin_id']]['vacation'] = $row['vacation'];
                $admins[$row['admin_id']]['overtime'] = $row['overtime'];
            }
        }
        echo "<div id=\"room_info\" class=\"datatable_container\">\n";
        echo "<table id=\"rooms_table\" class=\"admin_table display\" style = \"font-size: 16px;\"> \n";
        echo "<thead>\n";
        echo "<tr>\n";
        $fields = array(
            'id' => '编号', 'name' => '姓名', 'sex' => '性别', 'duty' => '职务', 'overtime' => '当月加班时长',
            'vacation' => '当月休假余额', 'praise' => '当月好评数');
        foreach (array_values($fields) as $field) {

            echo "<th>$field</th>\n";
        }
        echo "<th>操作</th>\n";

        echo "</tr>\n";
        echo "</thead>\n";
        echo "<tbody>\n";
        $row_class = "odd";
        foreach ($admins as $k => $r) {
            $row_class = ($row_class == "even") ? "odd" : "even";
            echo "<tr class=\"$row_class\">\n";
            foreach (array_keys($fields) as $field) {
                switch ($field) {
                    // the standard MRBS fields
                    case 'id':
                    case 'name':

                        echo "<td><div>" . htmlspecialchars($r[$field]) . "</div></td>\n";
                        break;
                    case 'duty':
                        echo "<td><div  onclick='set_input_value(" . (int)$r['id'] . ',"'.$r['name'] ."\")'>" . htmlspecialchars($r[$field]) . "</div></td>\n";
                        break;
                    case 'overtime':
                        echo "<td ><div style='float: left;'>";
                        add_admin_overtime((int)$r['id'], $currentDate);
                        echo '</div><div style=\'float: left\'>' . htmlspecialchars($r[$field]) .
                            "</div></div></td>\n";
                        break;
                    case 'vacation':
                        echo "<td ><div style='float: left;'>";
                        minus_admin_vacation((int)$r['id'], (int)$r['vacation'], $currentDate);
                        echo '</div><div style=\'float: left\'>' .
                            htmlspecialchars($r[$field]) . "</div></div></td>\n";
                        break;
                    case 'praise':
                        echo "<td ><div style='float: left;'>";
                        add_admin_praise((int)$r['id'], $currentDate);
                        echo '</div><div style=\'float: left\'>' . htmlspecialchars($r[$field])
                            . "</div></div></td>\n";
                        break;
                    case 'sex':
                        echo "<td ><div>" . htmlspecialchars((int)$r['sex'] == 0 ? '女' : '男') . "</div></td>\n";
                        break;
                    // any user defined fields

                }  // switch
            }  // foreach

            // Give admins a delete button
            if ($is_admin) {
                echo "<td>\n<div>\n";
                generate_admin_delete_form((int)$r['id']);
                echo "</div>\n</td>\n";
            }

            echo "</tr>\n";

        }

        echo "</tbody>\n";
        echo "</table>\n";
        echo "</div>\n";


    }
} else {
    echo get_vocab("noarea");
}

// Give admins a form for adding rooms to the area - provided
// there's an area selected
//    if ($is_admin && $areas_defined && !empty($area)) {
generate_new_admin_form();
//    }
echo "</div>\n";
generate_edit_admin_form();