<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("You must be logged in to track a stock.", "warning");
    redirect("$BASE_PATH/login.php");
}

$stock_id = $_POST["stock_id"] ?? null;
if (!$stock_id || !is_numeric($stock_id)) {
    flash("Invalid stock ID.", "danger");
    redirect("list_stock.php");
}

$db = getDB();
try {
    $stmt = $db->prepare("INSERT INTO Tracker (user_id, stock_id) VALUES (:uid, :sid)");
    $stmt->execute([
        ":uid" => get_user_id(),
        ":sid" => $stock_id
    ]);
    flash("Stock added to your tracker.", "success");
} catch (PDOException $e) {
    flash("You already track this stock.", "warning");
}
redirect("list_tracker.php");
