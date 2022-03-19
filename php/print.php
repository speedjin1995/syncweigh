<?php

require_once 'db_connect.php';
// // Load the database configuration file 
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
}

if(isset($_POST['userID'], $_POST["file"])){
    if($_POST["file"] == 'weight'){
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Deleted"
            )
        );
    }
    else{
        echo json_encode(
            array(
                "status"=> "success", 
                "message"=> "Deleted"
            )
        );
    } 
}
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}

?>