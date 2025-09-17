<?php
include '../header.php';
session_start();

if (!isset($_SESSION['email_queue'])) {
    include 'eror.php';
    exit;
}
?>