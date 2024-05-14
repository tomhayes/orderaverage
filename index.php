<?php

// Set a few required variables...
$url = "https://fauxdata.codelayer.io/api/orders";
$data = fetchData($url);
$order_totals = [];

// Fetch data from the API
function fetchData($url) {

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    do {
        $response = curl_exec($curl);
        $data = json_decode($response, true);
    } while (!isset($data['orders']));

    curl_close($curl);

    return $data;
}

// Calculate the total price of the items in an order
function calculateTotalPrice($items) {
    $total_price = 0;

    foreach ($items as $item) {
        $total_price += $item['price'];
    }
    return $total_price;
}

// Calculate the mean average total price of all orders returned, rounded to 2 DPs
function calculateAverage($totals, $count) {
    return round(array_sum($totals) / $count, 2);
}

// Output the order data in a basic HTML table
echo "<table>";
echo "<tr><th>Order ID</th><th>Customer</th><th>Total Price</th></tr>";

foreach ($data['orders'] as $order) {
    $total_price = calculateTotalPrice($order['items']);
    $order_totals[] = $total_price;
    echo "<tr><td>{$order['id']}</td><td>{$order['customer']['name']}</td><td>{$total_price}</td></tr>";
}

echo "</table><br />";

// Display the total number of orders along with an average
$order_count = count($data['orders']);
echo "Number of orders: " . $order_count . "<br>";
echo "Mean average total price: " . calculateAverage($order_totals, $order_count) . "<br>";