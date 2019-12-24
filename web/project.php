<?php

namespace MRBS;

use MRBS\Form\ElementDiv;
use MRBS\Form\Form;
use MRBS\Form\ElementButton;
use MRBS\Form\ElementFieldset;
use MRBS\Form\ElementImg;
use MRBS\Form\ElementInputImage;
use MRBS\Form\ElementInput;
use MRBS\Form\Element;
use MRBS\Form\FieldInputNumber;
use MRBS\Form\FieldInputText;
use MRBS\Form\FieldInputSubmit;
use MRBS\Form\FieldSelect;

require "defaultincludes.inc";

#删除技师
function generate_project_delete_form($id)
{
    $form = new Form();

    $attributes = array('action' => 'del.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'delete_project',
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

#新增项目
function generate_new_project_form($typeName)
{
    $form = new Form();

    $attributes = array(
        'class' => 'form_admin standard',
        'action' => 'add.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' => 'add_project');
    $form->addHiddenInputs($hidden_inputs);


    // Visible fields
    $fieldset = new ElementFieldset();
    $fieldset->addLegend(get_vocab('添加项目'));

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("编号")
        ->setControlAttributes(array('id' => 'number',
            'name' => 'number',
            'required' => true,
            'maxlength' => 4));
    $div->addElement($field);

    // The description field
    $field = new FieldInputText();
    $field->setLabel("项目名称")
        ->setControlAttributes(array('id' => 'name',
            'name' => 'name',
            'required' => true,
            'maxlength' => 50));
    $div->addElement($field);
    $fieldset->addElement($div);

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("项目简称")
        ->setControlAttributes(array('id' => 'abbreviation',
            'name' => 'abbreviation',
            'required' => true,
            'maxlength' => 50));
    $div->addElement($field);

    // The description field

    $field = new FieldSelect();
    $field->setLabel("项目分类")
        ->addSelectOptions($typeName,1, true)
        ->setControlAttributes(array('name' => 'project_type'));
    $div->addElement($field);
    $fieldset->addElement($div);

    $div = new ElementDiv();
    // The name field
    $field = new FieldInputText();
    $field->setLabel("项目时长")
        ->setControlAttributes(array('id' => 'project_time',
            'name' => 'project_time',
            'required' => true,
            'maxlength' => 11));
    $div->addElement($field);

    // The description field
    $field = new FieldInputText();
    $field->setLabel("项目价格")
        ->setControlAttributes(array('id' => 'project_price',
            'name' => 'project_price',
            'required' => true,
            'maxlength' => 11));
    $div->addElement($field);
    $fieldset->addElement($div);


    // The submit button
    $field = new FieldInputSubmit();
    $field->setControlAttributes(array('value' => "添加项目",
        'class' => 'submit'));
    $fieldset->addElement($field);

    $form->addElement($fieldset);

    $form->render();
}

global $tbl_project;
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


print_header();

// Get the details we need for this area
$typeName= array(1 => 'SPA(1类)', 2 => '泡浴(2类)',3 => '足疗(3类)' , 4 => '推拿(4类)',5 => '面护(5类)',6 => '小项目(6类)');

echo "<h2>" . "项目列表" . "</h2>\n";


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
if ($is_admin || !empty($enabled_areas)) {

    echo "<div id=\"room_form\">\n";

    if (isset($area)) {
        $res = db()->query("SELECT * FROM `$tbl_project` WHERE 1  ORDER BY `id`");
        if ($res->count() == 0) {
            echo "<p>" . "没有项目" . "</p>\n";
        } else {
            $projects = array();
            for ($i = 0; ($row = $res->row_keyed($i)); $i++) {
                $projects[$row['id']] = $row;

            }

            echo "<div id=\"room_info\" class=\"datatable_container\">\n";
            echo "<table id=\"rooms_table\" class=\"admin_table display\">\n";
            echo "<thead>\n";
            echo "<tr>\n";
            $fields = array(
                'number' => '序号', 'name' => '项目名称', 'abbreviation' => '项目简称', 'type' => '项目分类', 'type_name' => '项目分类名称',
                'project_time' => '项目时长', 'project_price' => '项目价格'
            );
            foreach (array_values($fields) as $field) {

                echo "<th>$field</th>\n";
            }
            echo "<th>操作</th>\n";

            echo "</tr>\n";
            echo "</thead>\n";
            echo "<tbody>\n";
            $row_class = "odd";
            foreach ($projects as $k => $r) {
                $row_class = ($row_class == "even") ? "odd" : "even";
                echo "<tr class=\"$row_class\">\n";
                foreach (array_keys($fields) as $field) {
                    if($field != 'type_name'){
                        echo "<td><div>" . htmlspecialchars($r[$field]) . "</div></td>\n";
                    }else{
                        echo "<td><div>" . htmlspecialchars($typeName[$r['type']]) . "</div></td>\n";
                    }

                }  // foreach

                // Give admins a delete button
                if ($is_admin) {
                    echo "<td>\n<div>\n";
                    generate_project_delete_form((int)$r['id']);
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
    generate_new_project_form($typeName);
//    }
    echo "</div>\n";
}