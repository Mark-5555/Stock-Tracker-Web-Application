<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../partials/flash.php");
require_once(__DIR__ . "/../../../lib/redirect.php");

$id = $_GET["id"] ?? null;
if (!$id || !is_numeric($id)) {
  flash("Invalid ID");
  redirect("../list_stock.php");

}

$db = getDB();
$stmt = $db->prepare("DELETE FROM stocks WHERE id = :id");
$stmt->execute([":id" => $id]);
flash("Stock deleted successfully");
redirect("../list_stock.php");

