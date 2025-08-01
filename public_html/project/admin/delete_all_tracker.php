<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) redirect("login.php");
$db = getDB();
$stmt = $db->prepare("DELETE FROM Tracker WHERE user_id = :uid");
$stmt->execute([':uid' => get_user_id()]);
flash("All tracked stocks removed.");
redirect("list_tracker.php");
