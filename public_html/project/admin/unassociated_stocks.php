<?php
require_once(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../partials/flash.php");

if (!has_role("Admin")) {
    flash("Admins only", "warning");
    redirect("../../project/login.php");
}

$db = getDB();
$limit = isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] >= 1 && $_GET["limit"] <= 100 ? (int)$_GET["limit"] : 10;
$symbol_filter = trim($_GET["symbol"] ?? "");

// Count total unassociated stocks
$count_stmt = $db->prepare("SELECT COUNT(*) FROM stocks WHERE id NOT IN (SELECT stock_id FROM Tracker)");
$count_stmt->execute();
$total_unassociated = $count_stmt->fetchColumn();

$query = "SELECT * FROM stocks WHERE id NOT IN (SELECT stock_id FROM Tracker)";
$params = [];

if (!empty($symbol_filter)) {
    $query .= " AND symbol LIKE :sym";
    $params[":sym"] = "%$symbol_filter%";
}

$query .= " ORDER BY created DESC LIMIT $limit";
$stmt = $db->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Unassociated Stocks</h1>
<p>Showing <?= count($results) ?> of <?= $total_unassociated ?> unassociated stocks</p>

<form method="GET">
    <input name="symbol" placeholder="Filter by symbol" value="<?= htmlspecialchars($symbol_filter) ?>" />
    <input name="limit" type="number" min="1" max="100" value="<?= $limit ?>" />
    <input type="submit" value="Apply Filter" />
</form>

<?php if ($results): ?>
<table>
    <thead>
        <tr>
            <th>Symbol</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['symbol']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['end_date']) ?></td>
            <td><a href="../view_stock.php?id=<?= $row['id'] ?>">View</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No results available.</p>
<?php endif; ?>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
