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

if ($_SERVER["REQUEST_METHOD"] === "POST" && (strlen($stockTerm) || strlen($userTerm))) {
  if ($stockTerm) {
    $stmt = $db->prepare("SELECT id, symbol FROM stocks WHERE symbol LIKE :st LIMIT 25");
    $stmt->execute([":st" => "%$stockTerm%"]);
    $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if ($userTerm) {
    $stmt = $db->prepare("SELECT id, username FROM Users WHERE username LIKE :ut LIMIT 25");
    $stmt->execute([":ut" => "%$userTerm%"]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if (isset($_POST["associate"]) && is_array($_POST["stock_ids"]) && is_array($_POST["user_ids"])) {
    $stmt = $db->prepare("INSERT INTO Tracker (user_id, stock_id) VALUES(:uid, :sid)
        ON DUPLICATE KEY UPDATE user_id = user_id");
    foreach ($_POST["user_ids"] as $uid) {
      foreach ($_POST["stock_ids"] as $sid) {
        try {
          $stmt->execute([":uid" => $uid, ":sid" => $sid]);
        } catch (PDOException $e) {
          flash("Error managing association", "danger");
        }
      }
    }
    flash("Associations updated", "success");
    redirect("admin_assign_tracker.php");
  }
}
?>
<h1>Admin Stock Tracker Assignments</h1>
<form method="POST">
  <div>
    <label>Stock Symbol (partial):</label><input name="stock_term" value="<?=htmlspecialchars($stockTerm)?>" />
    <label>Username (partial):</label><input name="user_term" value="<?=htmlspecialchars($userTerm)?>" />
    <input type="submit" value="Search" />
  </div>

  <?php if ($stocks || $users): ?>
    <h3>Stocks</h3>
    <?php foreach ($stocks as $s): ?>
      <div>
        <input type="checkbox" name="stock_ids[]" value="<?=$s['id']?>" id="stock_<?=$s['id']?>" />
        <label for="stock_<?=$s['id']?>"><?=htmlspecialchars($s['symbol'])?></label>
      </div>
    <?php endforeach; ?>

    <h3>Users</h3>
    <?php foreach ($users as $u): ?>
      <div>
        <input type="checkbox" name="user_ids[]" value="<?=$u['id']?>" id="user_<?=$u['id']?>" />
        <label for="user_<?=$u['id']?>"><?=htmlspecialchars($u['username'])?></label>
      </div>
    <?php endforeach; ?>

    <button type="submit" name="associate">Toggle Associations</button>
  <?php endif; ?>
</form>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
