<?php
namespace MRBS;

require "../defaultincludes.inc";

http_headers(array("Content-type: application/x-javascript"),
             60*30);  // 30 minute expiry

if ($use_strict)
{
  echo "'use strict';\n";
}

?>
var isAdmin;

<?php
// Set (if set is true) or clear (if set is false) a timer
// to check for conflicts periodically in case someone else
// books the slot you are looking at.  If setting the timer
// it also performs an immediate check.
?>
window.onload=function (){
    $(".chosen-select").chosen()

}
