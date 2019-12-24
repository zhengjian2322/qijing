<?php

namespace MRBS;

use MRBS\Form\Form;
use MRBS\Form\ElementInputSubmit;

require "defaultincludes.inc";


function generate_no_form($url)
{
    $form = new Form();

    $attributes = array('action' => $url, //'massagist.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // The button
    $element = new ElementInputSubmit();
    $element->setAttribute('value', get_vocab("NO"));
    $form->addElement($element);

    $form->render();
}


function generate_yes_form($id, $action)
{
    $form = new Form();

    $attributes = array('action' => 'del.php',
        'method' => 'post');

    $form->setAttributes($attributes);

    // Hidden inputs
    $hidden_inputs = array('type' =>  $action,//'delete_massagist',
        'id' => $id,
        'confirm' => '1');
    $form->addHiddenInputs($hidden_inputs);

    // The button
    $element = new ElementInputSubmit();
    $element->setAttribute('value', get_vocab("YES"));
    $form->addElement($element);

    $form->render();
}




// Check the user is authorised for this page
checkAuthorised();

// Get non-standard form variables
$type = get_form_var('type', 'string');
$confirm = get_form_var('confirm', 'string', null, INPUT_POST);

// This is gonna blast away something. We want them to be really
// really sure that this is what they want to do.

if ($type == "delete_massagist") {
    $id = get_form_var('id', 'int');
    if (!empty($confirm)) {

        db()->begin();
        try {
            db()->command("DELETE FROM `mrbs_massagist` WHERE `id` = ? ", array($id));
        } catch (DBException $e) {
            db()->rollback();
            throw $e;
        }

        db()->commit();
        header("Location: massagist.php");
        exit;
    } else {
        print_header();


        echo "<div id=\"del_room_confirm\">\n";
        echo "<p>" . "确定嘛？" . "</p>\n";

        generate_yes_form($id, 'delete_massagist');
        generate_no_form('massagist.php');

        echo "</div>\n";
        output_trailer();
        exit;
    }
}else if ($type == "delete_admin") {
    global $tbl_admin;
    $id = get_form_var('id', 'int');
    if (!empty($confirm)) {
        db()->begin();
        try {
            db()->command("DELETE FROM `$tbl_admin` WHERE `id` = ? ", array($id));
        } catch (DBException $e) {
            db()->rollback();
            throw $e;
        }

        db()->commit();
        header("Location: admins.php");
        exit;
    } else {
        print_header();

        echo "<div id=\"del_room_confirm\">\n";
        echo "<p>" ."确定嘛？". "</p>\n";

        generate_yes_form($id, 'delete_admin');
        generate_no_form('admins.php');

        echo "</div>\n";
        output_trailer();
        exit;
    }
}else if($type == "delete_project"){
    global $tbl_project;
    $id = get_form_var('id', 'int');
    if (!empty($confirm)) {
        db()->begin();
        try {
            db()->command("DELETE FROM `$tbl_project` WHERE `id` = ? ", array($id));
        } catch (DBException $e) {
            db()->rollback();
            throw $e;
        }

        db()->commit();
        header("Location: project.php");
        exit;
    } else {
        print_header();

        echo "<div id=\"del_room_confirm\">\n";
        echo "<p>" ."确定嘛？". "</p>\n";

        generate_yes_form($id, 'delete_project');
        generate_no_form('project.php');

        echo "</div>\n";
        output_trailer();
        exit;
    }
}

if ($type == "area") {
    // We are only going to let them delete an area if there are
    // no rooms. its easier
    $n = db()->query1("SELECT COUNT(*) FROM $tbl_room WHERE area_id=?", array($area));
    if ($n == 0) {
        // OK, nothing there, lets blast it away
        db()->command("DELETE FROM $tbl_area WHERE id=?", array($area));

        // Redirect back to the admin page
        header("Location: admin.php");
        exit;
    } else {
        // There are rooms left in the area
        print_header($day, $month, $year, $area, isset($room) ? $room : null);
        echo "<p>\n";
        echo get_vocab("delarea");
        echo "<a href=\"admin.php\">" . get_vocab("backadmin") . "</a>";
        echo "</p>\n";
        output_trailer();
        exit;
    }
}

throw new \Exception ("Unknown type");

