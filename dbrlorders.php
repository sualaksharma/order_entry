<!DOCTYPE html>
<html>
<head>
    <title>Order Entry</title>
    <style>
        /* Reset default margin and padding for all elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Set a background color for the entire page */
        body {
            background-color: #f4f4f4;
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Style the container */
        .container {
            width: 300px; /* Set your desired width */
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        /* Style the heading */
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Style the form */
        form {
            text-align: center;
        }

        /* Style the labels */
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        /* Style the select input */
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        /* Style the number input */
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        /* Style the submit button */
        input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        /* Change submit button color on hover */
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Entry</h1>
        <form action="process_order.php" method="POST">
            <label for="telecaller">Telecaller:</label>
            
            <!-- Add a "Select" option as the default -->
            <select name="telecaller" id="telecaller">
                <option value="">Select</option>
                <?php
                // Establish a database connection (mysqli or PDO)
                $db = new mysqli("184.168.97.210", "wk8divcqwwyu", "Sualaksharma@291100", "i7715383_wp2");

                if ($db->connect_error) {
                    die("Connection failed: " . $db->connect_error);
                }

                // Fetch the list of telecallers from the database
                $sql = "SELECT id, name FROM telecallers";
                $result = $db->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                    }
                }

                $db->close();
                ?>
            </select>
            <label for="orderCount">Order Count:</label>
            <input type="number" name="orderCount" id="orderCount" required>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
