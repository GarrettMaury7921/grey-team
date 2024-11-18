<?php

$sname= "sql1";
$unmae= "haiku_user";
$password = "epicgarrett";

$db_name = "clients";

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
	echo "Connection failed!";
}