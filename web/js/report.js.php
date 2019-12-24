<?php
namespace MRBS;

require "../defaultincludes.inc";

http_headers(array("Content-type: application/x-javascript"),
    60 * 30);  // 30 minute expiry



// =================================================================================


// Extend the init() function 
?>

$(window).on('load', function() {
    $("#export_start").datepicker();
    $("#export_end").datepicker();
    $("#massagist_start").datepicker();
    $("#massagist_end").datepicker();
    $("#admin_start").datepicker();
    $("#admin_end").datepicker();
});


