<?php
require_once(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../partials/flash.php");
require_once(__DIR__ . "/../../lib/db.php");
if (!is_logged_in()) redirect("login.php");

$db = getDB();
$uid = get_user_id();

// Handle filters
$symbol = trim($_GET['symbol'] ?? '');
$interval = trim($_GET['interval'] ?? '');
$limit = intval($_GET['limit'] ?? 10);
$limit = ($limit >= 1 && $limit <= 100) ? $limit : 10;

$where = "WHERE t.user_id = :uid";
$params = [":uid" => $uid];

if (!empty($symbol)) {
    $where .= " AND s.symbol LIKE :symbol";
    $params[":symbol"] = "%$symbol%";
}

if (!empty($interval)) {
    $where .= " AND s.interval = :interval";
    $params[":interval"] = $interval;
}

// Get total count before applying limit
$stmt = $db->prepare("SELECT COUNT(*) as total FROM Tracker t JOIN stocks s ON t.stock_id = s.id $where");
$stmt->execute($params);
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Apply limit
$where .= " ORDER BY t.created DESC LIMIT :limit";
$params[":limit"] = $limit;

$stmt = $db->prepare("SELECT t.id as tracker_id, s.* FROM Tracker t JOIN stocks s ON t.stock_id = s.id $where");
foreach ($params as $k => $v) {
    if ($k === ":limit") {
        $stmt->bindValue($k, $v, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($k, $v);
    }
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>My Tracked Stocks</h1>
<p>Showing <?= count($results) ?> of <?= $total ?> result(s)</p>
<a href="admin/delete_all_tracker.php" onclick="return confirm('Remove all tracked stocks?')">Remove All</a>
<form method="GET">
  <label>Symbol: <input type="text" name="symbol" value="<?= htmlspecialchars($symbol) ?>" /></label>
  <label>Interval: <input type="text" name="interval" value="<?= htmlspecialchars($interval) ?>" /></label>
  <label>Limit (1-100): <input type="number" name="limit" min="1" max="100" value="<?= $limit ?>" /></label>
  <input type="submit" value="Apply Filters" />
</form>
<?php if ($results): ?>
<table><thead><tr><th>Symbol</th><th>Interval</th><th>Dates</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($results as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['symbol']) ?></td>
  <td><?= htmlspecialchars($row['interval']) ?></td>
  <td><?= $row['start_date'] ?> - <?= $row['end_date'] ?></td>
  <td>
    <a href="../view_stock.php?id=<?= $row['id'] ?>">View</a> |
    <a href="admin/delete_tracker.php?id=<?= $row['tracker_id'] ?>" onclick="return confirm('Untrack?')">Untrack</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php else: ?>
<p>No tracked stocks found that match your filters.</p>
<?php endif; ?>