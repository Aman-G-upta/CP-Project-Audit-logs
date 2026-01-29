<?php
$conn = new mysqli("localhost","root","","audit_db");

if($conn->connect_error){
    die("Connection Failed".$conn->connect_error);
}
// else{
//     echo"Data base connected successfully ";
// }

?>