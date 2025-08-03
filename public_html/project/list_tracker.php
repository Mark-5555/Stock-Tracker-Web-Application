<?php
require_once(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../partials/flash.php");
require_once(__DIR__ . "/../../lib/db.php");
if (!is_logged_in()) redirect("login.php");
$db = getDB();
$stmt = $db->prepare("SELECT t.id as tracker_id, s.* FROM Tracker t JOIN stocks s ON t.stock_id = s.id WHERE t.user_id = :uid ORDER BY t.created DESC");
$stmt->execute([':uid' => get_user_id()]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>My Tracked Stocks</h1>
<p>Total: <?= count($results) ?></p>
<a href="admin/delete_all_tracker.php" onclick="return confirm('Remove all tracked stocks?')">Remove All</a>
<?php if ($results): ?>
<table><thead><tr><th>Symbol</th><th>Dates</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($results as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['symbol']) ?></td>
  <td><?= $row['start_date'] ?> - <?= $row['end_date'] ?></td>
  <td>
    <a href="../view_stock.php?id=<?= $row['id'] ?>">View</a> |
    <a href="admin/delete_tracker.php?id=<?= $row['tracker_id'] ?>" onclick="return confirm('Untrack?')">Untrack</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php else: ?>
<p>No tracked stocks found.</p>
<?php endif; ?>