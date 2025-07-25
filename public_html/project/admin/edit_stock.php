<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../partials/flash.php");
require_once(__DIR__ . "/../../../lib/redirect.php");

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
redirect("../list_stock.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $symbol = $_POST["symbol"] ?? "";
    $interval = $_POST["interval"] ?? "";
    $start = $_POST["start_date"] ?? "";
    $end = $_POST["end_date"] ?? "";

    $stmt = $db->prepare("UPDATE stocks SET symbol = :s, `interval` = :i, start_date = :sd, end_date = :ed WHERE id = :id");
    $stmt->execute([
      ":s" => $symbol,
      ":i" => $interval,
      ":sd" => $start,
      ":ed" => $end,
      ":id" => $id
    ]);
    flash("Stock updated");
    redirect("edit_stock.php?id=$id");
}
?>
<h1>Edit Stock</h1>
<form method="POST">
  <label>Symbol</label>
  <input name="symbol" required value="<?= htmlspecialchars($stock["symbol"]) ?>" />
  <label>Interval</label>
  <input name="interval" required value="<?= htmlspecialchars($stock["interval"]) ?>" />
  <label>Start Date</label>
  <input type="date" name="start_date" required value="<?= $stock["start_date"] ?>" />
  <label>End Date</label>
  <input type="date" name="end_date" required value="<?= $stock["end_date"] ?>" />
  <input type="submit" value="Update" />
</form>
<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
