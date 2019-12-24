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


//#更新房间状态
//function convert_room_statue($id, $status)
//{
//    $form = new Form();
//    $attributes = array('action' => 'update.php',
//        'method' => 'post');
//    $form->setAttributes($attributes);
//    // Hidden inputs
//    $hidden_inputs = array('type' => 'update_room_status',
//        'id' => $id,
//        'status' => $status);
//    $form->addHiddenInputs($hidden_inputs);
//    // The button
//    $element = new ElementInputImage();
//    $element->setAttributes(array('class' => 'button',
//        'src' => 'images/repeat.png',
//        'width' => '16',
//        'height' => '16',
////        'title' => get_vocab('delete'),
////        'alt' => get_vocab('delete')
//    ));
//    $form->addElement($element);
//    $form->render();
//}

global $tbl_rooms;
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

echo "<h2>" . "房间列表" . "</h2>\n";


// Now the custom HTML
if ($auth['allow_custom_html']) {
    echo "<div id=\"div_custom_html\">\n";
    // no htmlspecialchars() because we want the HTML!
    echo (isset($custom_html)) ? "$custom_html\n" : "";
    echo "</div>\n";
}



if ($is_admin || !empty($enabled_areas)) {

    echo "<div id=\"room_form\">\n";
    $currentDate = (string)date('Y-m' , time());
    if (isset($area)) {
        $res = db()->query("SELECT * FROM `$tbl_rooms` WHERE 1  ORDER BY `id`");

        if ($res->count() == 0) {
            echo "<p>" . "没有房间" . "</p>\n";
        } else {
            $rooms = array();
            for ($i = 0; ($row = $res->row_keyed($i)); $i++) {
                $rooms[$row['id']] = $row;
            }

            echo "<div id=\"room_info\" class=\"datatable_container\">\n";
            echo "<table id=\"rooms_table\" class=\"admin_table display\" style = \"font-size: 16px;\">\n";
            echo "<thead>\n";
            echo "<tr>\n";
            $fields = array(
                'number' => '编号', 'topic' => '主题','capacity' => '容量', 'type' => '房间类别' );
            foreach (array_values($fields) as $field) {

                echo "<th>$field</th>\n";
            }


            echo "</tr>\n";
            echo "</thead>\n";
            echo "<tbody>\n";
            $row_class = "odd";
            foreach ($rooms as $k => $r) {
                $row_class = ($row_class == "even") ? "odd" : "even";
                echo "<tr class=\"$row_class\">\n";
                foreach (array_keys($fields) as $field) {
                    switch ($field ) {
                        // the standard MRBS fields
                        case 'topic':
                        case 'capacity':
                        case 'number':
                        case 'type':
                            echo "<td><div>" . htmlspecialchars($r[$field]) . "</div></td>\n";
                            break;

                    }  // switch
                }  // foreach



                echo "</tr>\n";

            }

            echo "</tbody>\n";
            echo "</table>\n";
            echo "</div>\n";


        }
    } else {
        echo get_vocab("noarea");
    }

    echo "</div>\n";
}