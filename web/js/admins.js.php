<?php
namespace MRBS;

require "../defaultincludes.inc";

http_headers(array("Content-type: application/x-javascript"),
             60*30);  // 30 minute expiry

if ($use_strict)
{
  echo "'use strict';\n";
}

// =================================================================================

// Extend the init() function 
?>
function chang_admin_role() {
    if($("#edit_id").val() == '' || $("#edit_id").val() == null){
        alert("请点击级别一列，选择需要操作的技师");
        return ;
    }
    $('#chang_admin_role').submit();
}
function set_input_value(admin_id, name) {
    //if($("#update_massagist_rank").attr('hidden')){
    //    $("#update_massagist_rank").attr('hidden', false);
        $("#edit_id").val(admin_id);
        $("#edit_name").val(name);
    //}else{
    //    $("#update_massagist_rank").attr('hidden', true);
    //    $("#edit_id").val("");
    //    $("#edit_name").val("");
    //}

}