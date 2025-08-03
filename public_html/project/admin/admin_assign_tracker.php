<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../partials/flash.php");
require_once(__DIR__ . "/../../../lib/redirect.php");

if (!has_role("Admin")) {
  flash("Admins only", "warning");
  redirect("../../project/login.php");
}

$db = getDB();
$stockTerm = $_POST["stock_term"] ?? "";
$userTerm = $_POST["user_term"] ?? "";
$stocks = $users = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Search stock symbols
  if ($stockTerm) {
    $stmt = $db->prepare("SELECT id, symbol FROM stocks WHERE symbol LIKE :st LIMIT 25");
    $stmt->execute([":st" => "%$stockTerm%"]);
    $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Search usernames
  if ($userTerm) {
    $stmt = $db->prepare("SELECT id, username FROM Users WHERE username LIKE :ut LIMIT 25");
    $stmt->execute([":ut" => "%$userTerm%"]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  if (
    isset($_POST["associate"]) &&
    isset($_POST["stock_ids"], $_POST["user_ids"]) &&
    is_array($_POST["stock_ids"]) &&
    is_array($_POST["user_ids"])
  ) {
    foreach ($_POST["user_ids"] as $uid) {
      foreach ($_POST["stock_ids"] as $sid) {
        // Check if association exists
        $checkStmt = $db->prepare("SELECT id FROM Tracker WHERE user_id = :uid AND stock_id = :sid");
        $checkStmt->execute([":uid" => $uid, ":sid" => $sid]);
        $exists = $checkStmt->fetch();

        if ($exists) {
          // Delete if exists
          $deleteStmt = $db->prepare("DELETE FROM Tracker WHERE user_id = :uid AND stock_id = :sid");
          try {
            $deleteStmt->execute([":uid" => $uid, ":sid" => $sid]);
          } catch (PDOException $e) {
            flash("Error removing: " . $e->getMessage(), "danger");
          }
        } else {
          // Insert if not exists
          $insertStmt = $db->prepare("INSERT INTO Tracker (user_id, stock_id) VALUES (:uid, :sid)");
          try {
            $insertStmt->execute([":uid" => $uid, ":sid" => $sid]);
          } catch (PDOException $e) {
            flash("Error adding: " . $e->getMessage(), "danger");
          }
        }
      }
    }
    flash("Associations toggled successfully", "success");
    redirect("admin_assign_tracker.php");
  }
}
?>

<h1>Admin Stock Tracker Assignments</h1>

<form method="POST">
  <div>
    <label>Stock Symbol (partial):</label>
    <input name="stock_term" value="<?= htmlspecialchars($stockTerm) ?>" />
    <label>Username (partial):</label>
    <input name="user_term" value="<?= htmlspecialchars($userTerm) ?>" />
    <input type="submit" value="Search" />
  </div>

  <?php if ($stocks || $users): ?>
    <h3>Stocks</h3>
    <?php foreach ($stocks as $s): ?>
      <div>
        <input type="checkbox" name="stock_ids[]" value="<?= $s['id'] ?>" id="stock_<?= $s['id'] ?>" />
        <label for="stock_<?= $s['id'] ?>"><?= htmlspecialchars($s['symbol']) ?></label>
      </div>
    <?php endforeach; ?>

    <h3>Users</h3>
    <?php foreach ($users as $u): ?>
      <div>
        <input type="checkbox" name="user_ids[]" value="<?= $u['id'] ?>" id="user_<?= $u['id'] ?>" />
        <label for="user_<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></label>
      </div>
    <?php endforeach; ?>

    <button type="submit" name="associate">Toggle Associations</button>
  <?php endif; ?>
</form>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>