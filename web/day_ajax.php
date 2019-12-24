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
checkAuthorised() ;



// (1) Check the user is authorised for this page
//  ---------------------------------------------
checkAuthorised();

// Also need to know whether they have admin rights



$id =  $_POST['id'];
$status = (int)$_POST['status'];

global $tbl_orders,$tbl_rooms,$tbl_massagist ;
if(isset($id) && !empty($id) && isset($status) && !empty($status)){
    $res = db()->query("SELECT `id`,`status`,`room_id`,`massagist` FROM $tbl_orders WHERE `id` = ?", array($id));
    $result = $res->all_rows_keyed();
    if (empty($res)) {

    }else {
        if (!empty($result[0]['massagist']) && $status == 1 ) {
            $massagist = $result[0]['massagist'];
            db()->command("UPDATE $tbl_massagist SET `status` = 0 WHERE `id` IN ($massagist )");

        }
        db()->command("UPDATE $tbl_orders SET `status` = ? WHERE `id` = ?",  array($status, $id));


    }

}


//$returl = "admin.php?area=$area" . (!empty($error) ? "&error=$error" : "");
//header("Location: $returl");
