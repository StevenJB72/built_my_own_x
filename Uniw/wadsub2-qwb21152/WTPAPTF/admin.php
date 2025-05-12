<!DOCTYPE html>
<html lang="en">
<!-- HEAD -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Baird's Art Store</title>
    <link rel="icon" type="image" href="Baird's-logos.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<!-- BODY -->
<body>
<?php
session_start();

$password = "WeKnowTheGame23";
$display_form = true;

$host = "devweb2023.cis.strath.ac.uk";
$user = "qwb21152";//your username
$pass = "Ooceigheo9ee";//your MySQL password
$dbname = $user;
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $entered_password = $_POST['password'];

    // Check if the entered password matches the correct one
    if ($entered_password === $password) {
        $_SESSION['is_authenticated'] = true; // Set a session variable
        $display_form = false;
    } else {
        $errorMsg = "Error: Incorrect password!";
    }
}

if (isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated']) {
    // User is authenticated, no need to show login form
    $display_form = false;
    if (isset($_POST['submit_delete'])) {
        if (isset($_POST['delete_id'])) {
            $delete_id = $_POST['delete_id'];
            $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                echo "<p>Order deleted successfully.</p>";
                header("Location: admin.php");
                exit();
            } else {
                echo "<p>Error deleting record: " . $conn->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Order ID not provided.</p>";
        }

        // After deletion, redirect back to admin.php
        header("Location: admin.php");
        exit();
    }

    // Your code to display admin content (orders, forms, etc.)
    // ...
    $sql = "SELECT * FROM orders";
    $db_data = $conn->query($sql);
    ?>
    <!--NAVBAR -->
    <nav class="bg-dark navbar-dark">
        <div class="container">
            <a href="index.php" class="navbar-brand"><i class="fas fa-tree mr-2">Home</i></a>
            <a href="admin.php" class="navbar-brand"><i class="fas fa-tree mr-2">Admin</i></a>
        </div>
    </nav>
    <section id="header" class="jumbotron text-center text-body-emphasis ">
        <h1 class="display-3">Baird's Admin</h1>
    </section>
    <div class = "container-fluid">
        <div class = "row justify-content-center">
            <div class="col-12">
                <div class="card border">
                    <div class="card-body">
                        <?php

                        if ($db_data->num_rows > 0) {
                            // Begin table structure
                            echo "<div class='table-responsive'>";
                            echo "<table class='table table-bordered border-primary align-middle'>";
                            echo "<thead>";
                            echo "<tr><th scope='col'>Order ID</th><th scope='col'>Art ID</th>
                                  <th scope='col'>Customer Name</th>
                                  <th scope='col'>Phone Number</th>
                                  <th scope='col'>Email</th>
                                  <th scope='col'>Postal Address</th>
                                  <th scope='col'> Delete Order </th></tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            // Fetch each row as an associative array
                            while ($row = $db_data->fetch_assoc()) {
                                // Output the data for each row
                                echo "<tr>" .
                                    "<td>" . htmlspecialchars($row["order_id"]) . "</td>" .
                                    "<td>" . htmlspecialchars($row["art_id"]) . "</td>" .
                                    "<td>" . htmlspecialchars($row["customer_name"]) . "</td>" .
                                    "<td>" . htmlspecialchars($row["phone_number"]) . "</td>" .
                                    "<td>" . htmlspecialchars($row["email"]) . "</td>" .
                                    "<td>" . htmlspecialchars($row["postal_address"]) . "</td>" .
                                    "<td> <form method='post'>" .
                                    "<input type='hidden' name='delete_id' value='" . $row["order_id"] . "'>" .
                                    "<input type='submit' name='submit_delete' value='Delete' class='btn btn-danger'>" .
                                    "</form>" . "</td>".
                                    "</td>" .
                                    "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";
                            echo "</div>";
                        } else {
                            echo "<p>No orders found.</p>";
                        }

                        ?>
                    </div>
                </div>
            </div>

            <div class = "col-12 mt-4">
                <div class = "card">
                    <div class = "card-body">
                        <form method="post" enctype="multipart/form-data" class="row gx-3 gy-2 align-items-center">
                            <h2>Add New Art Below:</h2>
                            <div class="col-md-3">
                                <label for="id">ID</label>
                                <input type="number" name="id" id="id" placeholder="eg:12345" class="form-control bg-dark-subtle" required>
                            </div>
                            <div class="col-md-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" placeholder="Steven" class="form-control bg-dark-subtle" required>
                            </div>
                            <div class="col-md-3">
                                <label for="DOC">Date Completed</label>
                                <input type="date" name="DOC"  id="DOC" class="form-control bg-dark-subtle" required>
                            </div>
                            <div class="col-md-3">
                                <label for="width">Width</label>
                                <input type="number" name="width" id="width" placeholder="mm." class="form-control bg-dark-subtle" required>
                            </div>
                            <div class="col-md-3">
                                <label for="height">Height</label>
                                <input type="number" name="height" id="height" placeholder="mm." class="form-control bg-dark-subtle" required>
                            </div>
                            <div class="col-md-3">
                                <label for="price">Price</label>
                                <input type="number" name="price" id="price" placeholder="£" class="form-control bg-dark-subtle" required>
                            </div>
                            <div class="col-md-3">
                                <label for="desc">Description</label>
                                <input type="text" name="description" id="desc" class="form-control bg-dark-subtle" required>
                            </div>
                            <div class="col-auto">
                                <label for="image">Use JPEG</label>
                                <input type="file"  name="image" id="image">
                            </div>
                            <div class="col-auto">
                                <label for="submit">Add Art</label>
                                <input type="submit"  name="submit-add-art" id="submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php
} else {
    $errorMsg = "Error: Incorrect password!";
}

if(isset($_POST['submit-add-art'])){

    $id = $_POST['id'];
    $name = $_POST['name'];
    $date = $_POST['DOC'];
    $width = $_POST['width'];
    $height = $_POST['height'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $image = $_FILES['image']['tmp_name'];
        $fp = fopen($image, 'rb');
        $imageContent = fread($fp, filesize($image));
        fclose($fp);
    } else {
        // Handle error or set default image content
        $imageContent = null;
    }


    $stmt = $conn->prepare("INSERT INTO art(id,name,date_of_completion,width,height,price,description,art_image)
                                  VALUES (?,?,?,?,?,?,?,?) ");
    if($stmt === false){
        die("Error in preparing statement: " . $conn->error);
    }

    $null = NULL;
    $stmt->bind_param("issiiisb", $id, $name, $date, $width, $height, $price, $description, $null);
    $stmt->send_long_data(7, $imageContent);
    if($stmt->execute()){
        echo "<h3 class='text-center'>New Art Added Successfully. </h3>";
    } else {
        echo "Error in executing statement: " . $stmt->error;
    }
    $stmt->close();
}


?>

<?php
if($display_form){ ?>
    <div style="display: flex; justify-content: center;">
    <div class="card-body">
    <form action="admin.php" method="post">
        <label>Password: <input type="password" name="password" required></label>
        <input type="submit" name="submit">
    </form>
    </div>
</div>
<?php }
?>
</body>
</html>