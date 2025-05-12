<!-- START OF HTML -->
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

    <style>
        .card-container {
            padding: 15px;
        }
        .card {
            margin: 0 auto; /* Center the card */
        }
    </style>
</head>
<body>

<nav class="bg-dark navbar-dark">
    <div class="container">
        <a href="index.php" class="navbar-brand"><i class="fas fa-tree mr-2">Home</i></a>
        <a href="admin.php" class="navbar-brand"><i class="fas fa-tree mr-2">Admin</i></a>
    </div>
</nav>

<section id="header" class="jumbotron text-center">
    <h1 class="display-3">Baird's</h1>
    <p class="lead">Welcome to Baird's art store</p>
</section>

<?php
//DATABASE INITIALIZER
$host = "devweb2023.cis.strath.ac.uk";
$user = "qwb21152";
$pass = "Ooceigheo9ee";
$dbname = $user;
$conn = new mysqli($host, $user, $pass, $dbname);
$recordsPerPage = 12;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $recordsPerPage;

$sql_pull = "SELECT * FROM art LIMIT $recordsPerPage OFFSET $offset";
$db_data = $conn->query($sql_pull);

$result = $conn->query("SELECT COUNT(id) AS total FROM art");
$row = $result->fetch_assoc();
$totalPages = ceil($row['total'] / $recordsPerPage);

    // Check if there are any rows returned by the query
    if ($db_data->num_rows > 0) {
    // Fetch each row as an associative array
echo '<div class="container-fluid "> <div class="row">';
    while ($row = $db_data->fetch_assoc()) {
        ?>
<div class = "col-lg-4 col-md-6 card-container">
    <div class="card text-center mb-3" style="width: 350px;">
        <div class="d-flex justify-content-center align-items-center" style="width: 350px; height: 350px;">
            <?php
            $imageData = base64_encode($row['art_image']);
            $src = 'data:image/jpeg;base64,' . $imageData;
            echo '<img src="' . $src . '" class="img-fluid" alt="art image" style="max-width: 100%; max-height: 100%; border-radius: 5px;">';
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
                        <form method="post" action="order.php">
                            <input type="hidden" name="artwork_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-success">Order</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

</div>
        <?php
    }
    echo ' </div>';
        } else {
            // Message displayed if there are no records found
            echo "<tr><td colspan='6'>No records found or no more records to load</td></tr>";
    }
        ?>


<nav aria-label="Page navigation example">
    <ul class="pagination pagination-lg justify-content-center">
        <?php if($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page-1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>Previous
                </a>
            </li>
        <?php endif; ?>

        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page+1; ?>" aria-label="Next">
                    Next<span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
    </body>
    </html>