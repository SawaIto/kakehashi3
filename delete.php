<?php
session_start();
include("funcs.php");

if (!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id() || $_SESSION["kanri_flg"] != 1) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"];

$pdo = db_conn();
$sql = "DELETE FROM schedules WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status) {
    header("Location: select.php");
    exit;
}
?>