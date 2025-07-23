<?php
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../partials/flash.php");
require_once(__DIR__ . "/../../lib/redirect.php");

$id = $_GET["id"] ?? null;
if (!$id || !is_numeric($id)) {
  flash("Invalid ID");
  redirect("list_stocks.php");
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM stocks WHERE id = :id");
$stmt->execute([":id" => $id]);
$stock = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$stock) {
  flash("Stock not found");
  redirect("list_stocks.php");
}
?>
<h1>Stock Details</h1>
<ul>
  <li><strong>Symbol:</strong> <?= htmlspecialchars($stock["symbol"]) ?></li>
  <li><strong>Interval:</strong> <?= htmlspecialchars($stock["interval"]) ?></li>
  <li><strong>Date Range:</strong> <?= $stock["start_date"] ?> to <?= $stock["end_date"] ?></li>
</ul>
<a href="edit_stock.php?id=<?= $stock["id"] ?>">Edit</a> |
<a href="delete_stock.php?id=<?= $stock["id"] ?>" onclick="return confirm('Are you sure?')">Delete</a>
<?php require(__DIR__ . "/../../partials/flash.php"); ?>

