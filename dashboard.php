<?php
require_once "config.php";
if (!is_logged()) header("Location: index.php");
$user = current_user($db);
if (!$user) { session_destroy(); header("Location: index.php"); exit; }
if ($user['role']==='CLIENTE') header("Location: client.php");
else header("Location: diarista.php");
