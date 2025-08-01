<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) redirect("login.php");
$id = $_GET["id"] ?? null;
if (!$id || !is_numeric($id)) {
    flash("Invalid tracker ID.");
    redirect("list_tracker.php");
}

$db = getDB();
$stmt = $db->prepare("DELETE FROM Tracker WHERE id = :id AND user_id = :uid");
$stmt->execute([':id' => $id, ':uid' => get_user_id()]);
flash("Untracked successfully.");
redirect("list_tracker.php");