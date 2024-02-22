<?php
// Establish a database connection (mysqli or PDO)
$db = new mysqli("184.168.97.210", "wk8divcqwwyu", "Sualaksharma@291100", "i7715383_wp2");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

// Fetch orders for the specified date range
$sql = "SELECT order_date, telecaller_id, SUM(order_count) AS total_orders FROM orders
        WHERE order_date BETWEEN ? AND ?
        GROUP BY order_date, telecaller_id
        ORDER BY order_date, telecaller_id";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an associative array to store daily totals
$dailyTotals = array();

echo "<html>";
echo "<head>";
echo "<title>Order Report</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; }";
echo "h2 { margin-top: 20px; }";
echo "table { width: 100%; border-collapse: collapse; margin-top: 10px; }";
echo "table, th, td { border: 1px solid #ccc; }";
echo "th, td { padding: 10px; text-align: left; }";
echo "th { background-color: #f2f2f2; }";
echo "tr:nth-child(even) { background-color: #f2f2f2; }";
echo "td[rowspan] { vertical-align: top; }"; // Adjust vertical alignment for rowspan
echo "td.date-cell { font-weight: bold; }"; // Style date cells
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h2>Order Report for $startDate to $endDate</h2>";
echo "<table>";
echo "<tr><th>Date</th><th>Telecaller</th><th>Total Orders</th></tr>";

$currentDate = null; // Initialize to null
$totalOrdersPerDay = 0; // Initialize total orders for the day

while ($row = $result->fetch_assoc()) {
    $orderDate = $row['order_date'];
    $telecallerId = $row['telecaller_id'];
    $totalOrdersDay = $row['total_orders'];

    // Check if the date has changed
    if ($currentDate !== $orderDate) {
        // Display the date only once and calculate the daily total
        echo "<tr><td class='date-cell'>$orderDate</td>";
        $currentDate = $orderDate;
        $totalOrdersPerDay = 0; // Reset daily total for the new date
    } else {
        // Otherwise, leave the date cell empty
        echo "<tr><td></td>";
    }

    // Fetch telecaller name
    $sqlTelecaller = "SELECT name FROM telecallers WHERE id = ?";
    $stmtTelecaller = $db->prepare($sqlTelecaller);
    $stmtTelecaller->bind_param("i", $telecallerId);
    $stmtTelecaller->execute();
    $resultTelecaller = $stmtTelecaller->get_result();
    $telecallerName = ($resultTelecaller->fetch_assoc())['name'];

    echo "<td>$telecallerName</td><td>$totalOrdersDay</td></tr>";

    // Accumulate the daily total
    $totalOrdersPerDay += $totalOrdersDay;
}

echo "</table>";

// Display daily total orders
echo "<h2>Daily Total Orders</h2>";
echo "<table>";
echo "<tr><th>Date</th><th>Total Orders</th></tr>";

$stmt->execute();
$result = $stmt->get_result(); // Re-execute the query to reset the result set

$currentDate = null; // Initialize to null
$dailyTotal = 0; // Initialize daily total

while ($row = $result->fetch_assoc()) {
    $orderDate = $row['order_date'];
    $totalOrdersDay = $row['total_orders'];

    // Check if the date has changed
    if ($currentDate !== $orderDate) {
        // Display the date and the accumulated daily total
        if ($currentDate !== null) {
            echo "<tr><td>$currentDate</td><td>$dailyTotal</td></tr>";
        }
        $currentDate = $orderDate;
        $dailyTotal = $totalOrdersDay; // Reset daily total for the new date
    } else {
        // Accumulate the daily total
        $dailyTotal += $totalOrdersDay;
    }
}

// Display the daily total for the last date in the results
if ($currentDate !== null) {
    echo "<tr><td>$currentDate</td><td>$dailyTotal</td></tr>";
}

echo "</table>";

$stmt->close();
// Display total orders for each telecaller within the date range
echo "<h2>Total Orders by Telecaller</h2>";
echo "<table>";
echo "<tr><th>Telecaller</th><th>Total Orders</th></tr>";

$sqlTotalTelecallerOrders = "SELECT t.name AS telecaller_name, SUM(o.order_count) AS total_telecaller_orders
                             FROM telecallers t
                             LEFT JOIN orders o ON t.id = o.telecaller_id
                             WHERE o.order_date BETWEEN ? AND ?
                             GROUP BY t.name";
$stmtTotalTelecallerOrders = $db->prepare($sqlTotalTelecallerOrders);
$stmtTotalTelecallerOrders->bind_param("ss", $startDate, $endDate);
$stmtTotalTelecallerOrders->execute();
$resultTotalTelecallerOrders = $stmtTotalTelecallerOrders->get_result();

while ($rowTelecallerOrders = $resultTotalTelecallerOrders->fetch_assoc()) {
    $telecallerName = $rowTelecallerOrders['telecaller_name'];
    $totalTelecallerOrders = $rowTelecallerOrders['total_telecaller_orders'];

    echo "<tr><td>$telecallerName</td><td>$totalTelecallerOrders</td></tr>";
}

echo "</table>";

$stmtTotalTelecallerOrders->close();

echo "</body>";
echo "</html>";

$db->close();
?>