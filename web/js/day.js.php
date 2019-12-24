<?php
namespace MRBS;

require "../defaultincludes.inc";

http_headers(array("Content-type: application/x-javascript"),
    60 * 30);  // 30 minute expiry

if ($use_strict) {
    echo "'use strict';\n";
}

// =================================================================================


// Extend the init() function
?>

var newDoneNumber = <?php
$curentTimestamp = date("Y-m-d H:i:s", time());
$sql = "SELECT `id`,`end_timestamp`,`start_timestamp`,`status` FROM `mrbs_orders` WHERE (`status` = 2 OR `status` = 3) AND `end_timestamp` > ? AND `display` = 1  ";//
$res = db()->query($sql, array($curentTimestamp));
if(!empty($res)){
    $notDone = array();
    for ($i = 0; ($row = $res->row_keyed($i)); $i++) {
        $notDone[$i]['end_timestamp'] = strtotime($row['end_timestamp']);
        $notDone[$i]['start_timestamp'] = strtotime($row['start_timestamp']);
        $notDone[$i]['id'] = $row['id'];
        $notDone[$i]['done'] = 0;
        $notDone[$i]['ajax'] = 0;
    }
    echo json_encode($notDone);
}else{
echo "\"\"";
        }
        ?>;

function changeColor() {

    if (newDoneNumber != "") {
        for (var i = 0; i < newDoneNumber.length; i++) {
            if (newDoneNumber[i]['done'] == 1) {
                continue;
            }
            var a = Date.parse(new Date()) / 1000;
            console.log(a);
            console.log(newDoneNumber[i]['start_timestamp']);
            console.log(newDoneNumber[i]['end_timestamp']);

            if ((newDoneNumber[i]['end_timestamp'] <= a)) {
                $("div[data-id=" + newDoneNumber[i]['id'] + "]").each(function (a) {
                    if ($(this).attr("data-type") != 'E') {
                        $(this).attr("data-type", 'E');
                        $(this).parent().attr("class", 'E');
                    }
                });
                newDoneNumber[i]['done'] = 1;
                $.ajax({
                    url: 'day_ajax.php',
                    method: 'POST',
                    data: "id=" + newDoneNumber[i]['id'] + "&status=" + 1,
                    success: function success() {
                    },
                    error: function error() {

                    }
                });
            } else if (a >= newDoneNumber[i]['start_timestamp'] && a <= newDoneNumber[i]['end_timestamp']) {
                $("div[data-id=" + newDoneNumber[i]['id'] + "]").each(function (a) {
                    if ($(this).attr("data-type") != 'I') {
                        $(this).attr("data-type", 'I');
                        $(this).parent().attr("class", 'I');
                    }
                });

                if( a == $("input[name = 'massagist']").val()){
                    alert("请给将要进行的订单添加技师");
                }
                if (newDoneNumber[i]['ajax'] == 0) {
                    $.ajax({
                        url: 'day_ajax.php',
                        method: 'POST',
                        data: "id=" + newDoneNumber[i]['id'] + "&status=" + 2,
                        success: function success() {
                        },
                        error: function error() {

                        }
                    });
                    newDoneNumber[i]['ajax'] = 1;
                }
            } else {


            }

        }
    }
}


function setChosenValues() {
    var values = $('#chosenIds').val()
    if (values != null && values != '') {
        var chosenValues = values.split(',');
        for (var i = 0; i < chosenValues.length; i++) {
            alert(chosenValues[i]);
            $("select[name = 'massagist[]'] option[value = " + chosenValues[i] + "]").attr("selected", "selected");
            $("select[name = 'massagist[]']").trigger("chosen:updated");
        }
    }
    //alert(values);alert("sdf");
}
function invisible_order() {
    if (confirm("确定要删除吗？")) {
        $('#inv_order').submit();
        return true;
    } else {
        return false;

    }
}
function  send_message() {
    var phone_number = $("input[name = 'booking_phone' ]").val();
    if(phone_number == ""){
        alert("手机号为空，无法发送信息");
    }
    $.ajax({
        url: 'send_message.php',
        method: 'POST',
        data: "phone_number=" + phone_number,
        success: function success() {
        },
        error: function error() {

        }
    });
}
window.onload = function () {
    $(".chosen-select").chosen();
    //setChosenValues();
    setInterval(changeColor, 5000);

}


