<?php
require_once(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/redirect.php");

if (!has_role("Admin")) {
    flash("Admins only", "danger");
    redirect("../../project/login.php");
}

$id = $_GET["id"] ?? null;
if (!$id || !is_numeric($id)) {
    flash("Invalid tracker ID.");
    redirect("admin_tracker_list.php");
}

$db = getDB();
$stmt = $db->prepare("DELETE FROM Tracker WHERE id = :id");
try {
    $stmt->execute([':id' => $id]);
    flash("Tracker removed successfully.", "success");
} catch (PDOException $e) {
    flash("Error removing tracker: " . $e->getMessage(), "danger");
}

redirect("admin_tracker_list.php");
