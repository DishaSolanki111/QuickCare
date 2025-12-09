<?php
session_start();
echo isset($_SESSION['P_ID']) ? "LOGGED_IN" : "NOT_LOGGED_IN";
?>