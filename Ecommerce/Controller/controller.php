<?php
$valorop = $_GET['opcion'];

if ($valorop==1) {
    include("../view/login.html");
}
if($valorop==2){
    include("../view/register.html");
}
?>