<?php
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../partials/flash.php");
require_once(__DIR__ . "/../../lib/redirect.php");

$db = getDB();
$limit = isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] > 0 && $_GET["limit"] <= 100 ? (int)$_GET["limit"] : 10;
$symbol = isset($_GET["symbol"]) ? $_GET["symbol"] : "";

$query = "SELECT * FROM stocks WHERE 1=1";
$params = [];
if ($symbol) {
    $query .= " AND symbol LIKE :symbol";
    $params[":symbol"] = "%$symbol%";
}
$query .= " ORDER BY created DESC LIMIT $limit";
$stmt = $db->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Stock Records</h1>
<form method="GET">
  <input name="symbol" placeholder="Filter by symbol" value="<?= htmlspecialchars($symbol) ?>" />
  <input name="limit" type="number" min="1" max="100" value="<?= $limit ?>" />
  <input type="submit" value="Filter" />
</form>
<?php if ($results): ?>
<table>
  <thead><tr><th>Symbol</th><th>Interval</th><th>Dates</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($results as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row["symbol"]) ?></td>
      <td><?= htmlspecialchars($row["interval"]) ?></td>
      <td><?= $row["start_date"] ?> → <?= $row["end_date"] ?></td>
<td>
  <a href="view_stock.php?id=<?= $row["id"] ?>">View</a> |
  <a href="admin/edit_stock.php?id=<?= $row["id"] ?>">Edit</a> |
  <a href="admin/delete_stock.php?id=<?= $row["id"] ?>" onclick="return confirm('Delete this record?')">Delete</a> |
  <form method="POST" action="admin/create_tracker.php" style="display:inline;">
    <input type="hidden" name="stock_id" value="<?= $row["id"] ?>" />
    <button type="submit" style="background:none; border:none; color:cyan; text-decoration:underline; cursor:pointer; padding:0;">Track</button>
  </form>
</td>


    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
<p>No results available</p>
<?php endif; ?>
<?php require(__DIR__ . "/../../partials/flash.php"); ?>
