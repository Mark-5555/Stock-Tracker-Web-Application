
<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../lib/api_helpers.php");

$db = getDB();

putenv("STOCK_API_KEY=1abec7df91msh10bd030e28a4266p162e97jsna44bfca1a33f");

if (isset($_POST["symbol"], $_POST["interval"], $_POST["start_date"], $_POST["end_date"])) {
    $symbol = $_POST["symbol"];
    $interval = $_POST["interval"];
    $start = $_POST["start_date"];
    $end = $_POST["end_date"];

    // Save stock metadata
    $stmt = $db->prepare("INSERT INTO stocks (symbol, `interval`, start_date, end_date) VALUES (:symbol, :interval, :start, :end)");
    $stmt->execute([
        ":symbol" => $symbol,
        ":interval" => $interval,
        ":start" => $start,
        ":end" => $end
    ]);
    $stock_id = $db->lastInsertId();

    // API call to Twelve Data
    $params = [
        "symbol" => $symbol,
        "interval" => $interval,
        "start_date" => $start,
        "end_date" => $end
    ];
    $result = get("https://twelve-data1.p.rapidapi.com/time_series", "STOCK_API_KEY", $params, true, "twelve-data1.p.rapidapi.com");

    if (se($result, "status", 400, false) == 200) {
        $response = json_decode($result["response"], true);
        if (isset($response["values"])) {
            $stmt = $db->prepare("INSERT INTO time_series_data 
                (stock_id, datetime, open, high, low, close, volume) 
                VALUES (:sid, :dt, :open, :high, :low, :close, :volume)");
            foreach ($response["values"] as $row) {
                $stmt->execute([
                    ":sid" => $stock_id,
                    ":dt" => $row["datetime"],
                    ":open" => $row["open"],
                    ":high" => $row["high"],
                    ":low" => $row["low"],
                    ":close" => $row["close"],
                    ":volume" => $row["volume"]
                ]);
            }
            flash("Stock and time series data saved successfully!", "success");
        } else {
            flash("No time series data returned from API.", "warning");
        }
    } else {
        flash("API call failed (status: {$result["status"]})", "danger");
    }
}
?>

<div class="container-fluid">
    <h1>Insert Stock from API</h1>
    <form method="POST">
        <label>Symbol</label>
        <input name="symbol" required placeholder="e.g. AAPL" />
        <label>Interval</label>
        <input name="interval" required placeholder="e.g. 1day" />
        <label>Start Date</label>
        <input type="date" name="start_date" required />
        <label>End Date</label>
        <input type="date" name="end_date" required />
        <input type="submit" value="Fetch and Insert" />
    </form>
</div>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
