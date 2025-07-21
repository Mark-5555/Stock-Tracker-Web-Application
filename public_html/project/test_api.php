<?php
require(__DIR__ . "/../../partials/nav.php");
require_once(__DIR__ . "/../../lib/api_helpers.php");

// Set your RapidAPI key
putenv("STOCK_API_KEY=1abec7df91msh10bd030e28a4266p162e97jsna44bfca1a33f");

$result = [];
if (isset($_GET["symbol"], $_GET["interval"], $_GET["start_date"], $_GET["end_date"])) {
    $endpoint = "https://twelve-data1.p.rapidapi.com/time_series";
    $params = [
        "symbol"     => $_GET["symbol"],
        "interval"   => $_GET["interval"],
        "start_date" => $_GET["start_date"],
        "end_date"   => $_GET["end_date"],
        "outputsize" => 10, // optional
    ];
    $resultRaw = get($endpoint, "STOCK_API_KEY", $params, true, "twelve-data1.p.rapidapi.com");

    if ($resultRaw["status"] === 200) {
        $result = json_decode($resultRaw["response"], true);
    } else {
        $result = ["error" => "API call failed (HTTP {$resultRaw['status']})"];
    }
}
?>

<div class="container-fluid">
    <h1>Twelve Data Time Series</h1>
    <form>
        <div>
            <label>Symbol</label><input name="symbol" placeholder="e.g. AAPL" required />
            <label>Interval</label><input name="interval" placeholder="e.g. 1day" required />
            <label>Start Date</label><input name="start_date" type="date" required />
            <label>End Date</label><input name="end_date" type="date" required />
            <input type="submit" value="Fetch Data" />
        </div>
    </form>

    <?php if (!empty($result)) : ?>
        <?php if (isset($result["values"])): ?>
            <h3>Time Series for <?= htmlspecialchars($_GET["symbol"]) ?></h3>
            <table class="table">
                <thead><tr>
                    <th>Date</th><th>Open</th><th>High</th><th>Low</th><th>Close</th><th>Volume</th>
                </tr></thead>
                <tbody>
                <?php foreach ($result["values"] as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["datetime"]) ?></td>
                        <td><?= htmlspecialchars($row["open"]) ?></td>
                        <td><?= htmlspecialchars($row["high"]) ?></td>
                        <td><?= htmlspecialchars($row["low"]) ?></td>
                        <td><?= htmlspecialchars($row["close"]) ?></td>
                        <td><?= htmlspecialchars($row["volume"]) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($result["error"])): ?>
            <p class="text-danger">Error: <?= htmlspecialchars($result["error"]) ?></p>
        <?php else: ?>
            <pre><?= htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) ?></pre>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>
