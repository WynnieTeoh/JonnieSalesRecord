<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <title>Jonnie's Sales record</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>

<script>
        var selectedStatus = '<?php echo $selectedStatus; ?>';

        function filterByStatus() {
            var statusFilter = document.getElementById('statusFilter');
            selectedStatus = statusFilter.value;
            console.log('Selected Status:', selectedStatus);
        }
    </script>

</head>
<body class="blogdesire-body">
<?php
// Add the PHP code here
if(isset($_POST['submit'])){
    $date = "date:".$_POST['dates']."\n";
    $name = "name:".$_POST['clientname']."\n";
    $contact = "contact:".$_POST['phonenumber']."\n";
    $address = "address:".$_POST['addresses']."\n";
    $price = "price:".$_POST['amount']."\n";
    $status = "status:" . $_POST['status'] . "\n";
    $file = fopen("saved.txt", "a");
    die("Error opening file.");
    fwrite($file, $date);
    fwrite($file, $name);
    fwrite($file, $contact);
    fwrite($file, $address);
    fwrite($file, $price);
    fwrite($file, $status);
    fclose($file);
}

// Read saved data
$savedData = file_get_contents("saved.txt");
$entries = explode("\n", $savedData);

$selectedStatus = isset($_POST['status']) ? $_POST['status'] : 'all';
?>

    <div class="blogdesire-wrapper">
    <div class="blogdesire-heading">
        Jonnie's Sales Record
    </div>
    <form class="blogdesire-form" method="post">
        <input type="date" name="dates" placeholder="Date" required autocomplete="off"> <br>
        <input type="text" name="clientname" placeholder="Client Name" required autocomplete="off"> <br>
        <input type="tel" name="phonenumber" placeholder="Contact" required autocomplete="off"> <br>
        <input type="text" name="addresses" placeholder="Address" required autocomplete="off"> <br>
        <input type="number" name="amount" placeholder="Price" required autocomplete="off"> <br>
        <label for="status">Select Status:</label>
        <select id="status" name="status" required>
            <option value="unpaid">Unpaid</option>
            <option value="paiddeposit">Paid Deposit</option>
            <option value="workinprogress">Work in Progress</option>
            <option value="fullypaid">Fully Paid</option>
        </select>
        <br>
        <input type="submit" name="submit" value="SAVE" class="blogdesire-submit">
    </form>

    <div class="saved-data">
    <h2>Saved Data:</h2>
    <label for="status">Filter by Status:</label>
    <select id="statusFilter" name="status" onchange="filterByStatus()">
        <option value="all" <?php echo ($selectedStatus == 'all') ? 'selected' : ''; ?>>All</option>
        <option value="unpaid" <?php echo ($selectedStatus == 'unpaid') ? 'selected' : ''; ?>>Unpaid</option>
        <option value="paiddeposit" <?php echo ($selectedStatus == 'paiddeposit') ? 'selected' : ''; ?>>Paid Deposit</option>
        <option value="workinprogress" <?php echo ($selectedStatus == 'workinprogress') ? 'selected' : ''; ?>>Work in Progress</option>
        <option value="fullypaid" <?php echo ($selectedStatus == 'fullypaid') ? 'selected' : ''; ?>>Fully Paid</option>
    </select>

    <table>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Price</th>
            <th>Commission</th>
            <th>Status</th>
        </tr>


        <?php
        for ($i = 0; $i < count($entries); $i += 6) {
            $dateLine = explode(':', $entries[$i], 2);
            $nameLine = explode(':', $entries[$i + 1], 2);
            $contactLine = explode(':', $entries[$i + 2], 2);
            $addressLine = explode(':', $entries[$i + 3], 2);
            $priceLine = explode(':', $entries[$i + 4], 2);
            $statusLine = explode(':', $entries[$i + 5], 2);

            $date = isset($dateLine[1]) ? $dateLine[1] : '';
            $name = isset($nameLine[1]) ? $nameLine[1] : '';
            $contact = isset($contactLine[1]) ? $contactLine[1] : '';
            $address = isset($addressLine[1]) ? $addressLine[1] : '';
            $price = isset($priceLine[1]) ? $priceLine[1] : '';
            $status = isset($statusLine[1]) ? $statusLine[1] : '';
        
            echo "<form method='post' action='update_status.php'>";
            echo "<tr>
                      <td>$date</td>
                      <td>$name</td>
                      <td>$contact</td>
                      <td>$address</td>
                      <td>$price</td>
                      <td>" . number_format($price * 0.02, 2) . "</td>
                      <td>
                          <select class='status-dropdown' name='status[]'>
                              <option value='unpaid' " . ($status == 'unpaid' ? 'selected' : '') . ">Unpaid</option>
                              <option value='paiddeposit' " . ($status == 'paiddeposit' ? 'selected' : '') . ">Paid Deposit</option>
                              <option value='workinprogress' " . ($status == 'workinprogress' ? 'selected' : '') . ">Work in Progress</option>
                              <option value='fullypaid' " . ($status == 'fullypaid' ? 'selected' : '') . ">Fully Paid</option>
                          </select>
                          
                      </td>
                      <td><input type='hidden' name='entry_id' value='$i'><input type='submit' value='Update'></td>
                  </tr>";
            echo "</form>";
        }
        ?>
     </table>

     <?php
        // Calculate total price and total commission
        $totalPrice = 0; // Initialize total price variable
        $totalCommission = 0;
    
        if (count($entries) > 0) {
    for ($i = 0; $i < count($entries); $i += 6) {
        $priceLine = explode(':', $entries[$i + 4], 2);
        $totalPrice += isset($priceLine[1]) ? floatval($priceLine[1]) : 0;
        $totalCommission += isset($priceLine[1]) ? floatval($priceLine[1]) * 0.02 : 0;
    }  
}
        ?>
<!-- Display total price and total commission row -->
<table>
        <tr>
            <td colspan="4"></td>
            <th>Total Price:</th>
            <td colspan="2"><?php echo number_format($totalPrice, 2); ?></td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <th>Total Commission:</th>
            <td colspan="2"><?php echo number_format($totalCommission, 2); ?></td>
        </tr>
    </table>
</div>
    </div>
</body>
</html>
