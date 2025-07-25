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
$stmt = $db->prepare("SELECT * FROM stocks WHERE id = :id");
$stmt->execute([":id" => $id]);
$stock = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$stock) {
  flash("Stock not found");
  redirect("../list_stock.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $symbol = trim($_POST["symbol"] ?? "");
    $interval = trim($_POST["interval"] ?? "");
    $start = $_POST["start_date"] ?? "";
    $end = $_POST["end_date"] ?? "";

    // PHP validation
    $allowedIntervals = ["1day", "1h", "15min"];
    $validSymbol = preg_match("/^[A-Z.]{1,10}$/", $symbol);

    if (!$symbol || !$interval || !$start || !$end) {
        flash("All fields are required.");
    } elseif (!$validSymbol) {
        flash("Symbol must be 1-10 uppercase letters (A-Z, .)");
    } elseif (!in_array($interval, $allowedIntervals)) {
        flash("Interval must be one of: 1day, 1h, 15min");
    } elseif (strtotime($end) < strtotime($start)) {
        flash("End date must be after start date.");
    } else {
        $stmt = $db->prepare("UPDATE stocks SET symbol = :s, `interval` = :i, start_date = :sd, end_date = :ed WHERE id = :id");
        $stmt->execute([
            ":s" => $symbol,
            ":i" => $interval,
            ":sd" => $start,
            ":ed" => $end,
            ":id" => $id
        ]);
        flash("Stock updated successfully.", "success");
        redirect("edit_stock.php?id=$id");
    }
}
?>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelector("form").addEventListener("submit", function (e) {
    const symbol = document.querySelector("input[name='symbol']").value.trim();
    const interval = document.querySelector("input[name='interval']").value.trim();
    const startDate = new Date(document.querySelector("input[name='start_date']").value);
    const endDate = new Date(document.querySelector("input[name='end_date']").value);

    const validSymbol = /^[A-Z.]{1,10}$/.test(symbol);
    const allowedIntervals = ["1day", "1h", "15min"];

    let error = "";

    if (!validSymbol) {
      error += "Symbol must be uppercase letters (A-Z, .), up to 10 characters.\n";
    }

    if (!allowedIntervals.includes(interval)) {
      error += "Interval must be one of: 1day, 1h, 15min.\n";
    }

    if (startDate > endDate) {
      error += "Start date must be before or equal to end date.\n";
    }

    if (error) {
      e.preventDefault();
      alert(error);
    }
  });
});
</script>

<h1>Edit Stock</h1>
<form method="POST">
  <label>Symbol</label>
  <input name="symbol" required pattern="[A-Z.]{1,10}" title="1-10 uppercase letters or dots" value="<?= htmlspecialchars($stock["symbol"]) ?>" />

  <label>Interval</label>
  <input name="interval" required list="interval-options" value="<?= htmlspecialchars($stock["interval"]) ?>" />
  <datalist id="interval-options">
    <option value="1day">
    <option value="1h">
    <option value="15min">
  </datalist>

  <label>Start Date</label>
  <input type="date" name="start_date" required value="<?= $stock["start_date"] ?>" />

  <label>End Date</label>
  <input type="date" name="end_date" required value="<?= $stock["end_date"] ?>" />

  <input type="submit" value="Update" />
</form>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
