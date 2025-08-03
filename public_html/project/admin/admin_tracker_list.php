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
$limit = isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] >= 1 && $_GET["limit"] <= 100 ? (int)$_GET["limit"] : 10;
$username = $_GET["username"] ?? '';

// Get total count for stats
$total_query = "SELECT COUNT(*) as total FROM Tracker t JOIN Users u ON t.user_id = u.id WHERE 1=1";
$params = [];
if ($username) {
  $total_query .= " AND u.username LIKE :user";
  $params[":user"] = "%$username%";
}
$total_stmt = $db->prepare($total_query);
$total_stmt->execute($params);
$total_count = $total_stmt->fetch(PDO::FETCH_ASSOC)["total"] ?? 0;

// Main query with entity association count
$query = "SELECT t.id AS tracker_id, u.id AS user_id, u.username, s.id AS stock_id, s.symbol, s.start_date, s.end_date,
          (SELECT COUNT(*) FROM Tracker WHERE stock_id = s.id) AS total_associated
          FROM Tracker t
          JOIN Users u ON t.user_id = u.id
          JOIN stocks s ON t.stock_id = s.id
          WHERE 1=1";
if ($username) {
  $query .= " AND u.username LIKE :user";
}
$query .= " ORDER BY t.created DESC LIMIT $limit";
$stmt = $db->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>All User Trackers (Admin)</h1>
<p>Showing <?= count($results) ?> of <?= $total_count ?> results</p>
<form method="GET">
  <input name="username" placeholder="Filter username" value="<?= htmlspecialchars($username) ?>" />
  <input name="limit" type="number" min="1" max="100" value="<?= $limit ?>" />
  <input type="submit" value="Filter" />
</form>

<?php if ($results): ?>
  <table>
    <thead>
      <tr><th>User</th><th>Symbol</th><th>Dates</th><th>Associated Users</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($results as $r): ?>
      <tr>
        <td><a href="../../profile.php?id=<?= $r["user_id"] ?>"><?= htmlspecialchars($r["username"]) ?></a></td>
        <td><?= htmlspecialchars($r["symbol"]) ?></td>
        <td><?= $r["start_date"] ?> → <?= $r["end_date"] ?></td>
        <td><?= $r["total_associated"] ?></td>
        <td>
          <a href="../stocks/view_stock.php?id=<?= $r["stock_id"] ?>">View</a> |
          <a href="delete_tracker.php?id=<?= $r["tracker_id"] ?>" onclick="return confirm('Remove this tracker?')">Remove</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No results available</p>
<?php endif; ?>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>