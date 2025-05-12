<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Baird's Art Store</title>
    <link rel="icon" type="image" href="Baird's-logos.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
          crossorigin="anonymous">
</head>
<body>

<nav class="bg-dark navbar-dark">
    <div class="container">
        <a href="index.php" class="navbar-brand"><i class="fas fa-tree mr-2">Home</i></a>
        <a href="admin.php" class="navbar-brand"><i class="fas fa-tree mr-2">Admin</i></a>
    </div>
</nav>

<section id="header" class="jumbotron text-center text-body-emphasis ">
    <h1 class="display-3">Baird's Orders</h1>
</section>

<?php
$host = "devweb2023.cis.strath.ac.uk";
$user = "qwb21152";
$pass = "Ooceigheo9ee";
$dbname = $user;
$conn = new mysqli($host, $user, $pass, $dbname);
$art_id = ($_POST['artwork_id'] ?? null);

$errors = []; // Array to hold validation errors

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the required POST keys are set
    if (isset($_POST['artwork_id']) && isset($_POST['name']) && isset($_POST['phone-number']) && isset($_POST['email']) && isset($_POST['address'])) {
        $name = trim($_POST['name'] ?? '');
        $phone_number = trim($_POST['phone-number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Validate input
        if (!$name) {
            $errors['name'] = 'Name is required.';
        }

        if (!$phone_number) {
            $errors['phone-number'] = 'Phone number is required.';
        }

        if (!$email) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        if (!$address) {
            $errors['address'] = 'Address is required.';
        }

        // If there are no errors, proceed with database insertion
        if (count($errors) === 0) {
            $order_no = rand(1,3000);

            // Prepare statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO orders (order_id, art_id, customer_name, phone_number, email, postal_address) VALUES (?, ?, ?, ?, ?, ?)");
            if($stmt === false) {
                die("Error in preparing statement: " . $conn->error);
            }

            // Bind and execute the statement
            $stmt->bind_param("iissss", $order_no, $art_id, $name, $phone_number, $email, $address);
            if($stmt->execute()) {
                echo "<h3 class='text-center'>Order placed successfully. </h3>";
            } else {
                echo "Error in executing statement: " . $stmt->error;
            }
            $stmt->close();
        }
    }}

$db_data = $conn->prepare("SELECT * FROM art where id = ?");
$db_data->bind_param("i", $art_id);
$db_data->execute();

// Store the result and fetch the row
$result = $db_data->get_result();

?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="card justify-content-center border-dark">
                <h4> Enter your details below</h4>
                <form method="post" action="order.php" class="row gx-3 gy-2 align-items-center">
                    <input type="hidden" name="artwork_id" value="<?php echo htmlspecialchars($art_id ?? ''); ?>">
        <div class="col-mb-3">
            <label for="name">Name:</label>
            <input type="text" name ="name" id="name"  class="form-control " placeholder="Steven" >
            <?php if (isset($errors['name'])): ?>
                <div class="error"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>
        <div class="col-mb-3">
             <label for="phone-number">Phone:</label>
            <input type="text" name ="phone-number" id="phone-number" class="form-control" placeholder="07825473825" >
            <?php if (isset($errors['phone-number'])): ?>
                <div class="error"><?php echo $errors['phone-number']; ?></div>
            <?php endif; ?>
        </div>
        <div class="col-mb-3">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control " placeholder="abc@gmail.com" >
            <?php if (isset($errors['email'])): ?>
                <div class="error"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>
        <div class="col-mb-3">
            <label for="address">Address:</label>
            <input type="text" name ="address"  id="address" class="form-control " placeholder="Address" >
            <?php if (isset($errors['address'])): ?>
                <div class="error"><?php echo $errors['address']; ?></div>
            <?php endif; ?>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-success">Place Order</button>
        </div>
    </form>
</div>
</div>
 <div class="col-md-6 col-sm-12">
    <div class="card text-center">
        <div class="card-img">
            <?php
            if ($row = $result->fetch_assoc()) {
                $imageData = base64_encode($row['art_image']);
                $src = 'data:image/jpeg;base64,' . $imageData;
                echo '<img src="' . $src . '" class="img-fluid img-thumbnail" alt="art image" style="max-width: 100%; max-height: 100%; border-radius: 5px;">';
            } else {
                echo "No image found.";
            }

            $db_data->close();
            ?>
        </div>
        <div class = "card-body">
            <div class="card-title">
                <?php echo $row['name']; ?>
            </div>
            <p class="card-text"><?php echo $row['description']; ?></p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><?php echo $row['width'] . ' x ' . $row['height'] . ' mm' ?> </li>
                <li class="list-group-item"><?php echo 'Completed: ' .$row['date_of_completion']; ?></li>
                <li class="list-group-item">ID: <?php echo $row['id']; ?> Price:<?php echo '£'.$row['price']; ?></li>
                <li class="list-group-item">

                </li>
            </ul>
        </div>
    </div>
</div>
</div>
</div>

</body>
</html>
