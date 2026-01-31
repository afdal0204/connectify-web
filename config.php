<?php

$host = 'localhost';
$user = 'connectify';
$pass = 'connectify@12345';
$db = 'connectify-web';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}