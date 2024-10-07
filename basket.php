<?php
session_start();
##connects to database
include('db_connect.php');

##checks if the user is logged in, else sends to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

##Gets the user's basket including their past basket-adds.
$query_basket = "SELECT * FROM Basket WHERE user_id = ?";
$stmt_basket = $conn->prepare($query_basket);
$stmt_basket->bind_param("i", $user_id);
$stmt_basket->execute();
$result_basket = $stmt_basket->get_result();
$basket = $result_basket->fetch_assoc();

if (!$basket) {
    echo "<p>Your basket is empty.</p>";
    exit();
}

$basket_id = $basket['basket_id'];

##handles updating quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_quantity'])) {
        $content_id = $_POST['content_id'];
        $new_quantity = $_POST['quantity'];
        if ($new_quantity > 0) {
            $query_update = "UPDATE Basket_Content SET quantity = ? WHERE content_id = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("ii", $new_quantity, $content_id);
            $stmt_update->execute();
        } else {
            ##remove item if the amount is set to 0 (i.e. remove from basket)
            $query_delete = "DELETE FROM Basket_Content WHERE content_id = ?";
            $stmt_delete = $conn->prepare($query_delete);
            $stmt_delete->bind_param("i", $content_id);
            $stmt_delete->execute();
        }
    }
}

##get basket contents 
$query_content = "SELECT bc.content_id, p.product_name, p.price, bc.quantity 
                  FROM Basket_Content bc 
                  JOIN Product p ON bc.product_id = p.product_id 
                  WHERE bc.basket_id = ?";
$stmt_content = $conn->prepare($query_content);
$stmt_content->bind_param("i", $basket_id);
$stmt_content->execute();
$result_content = $stmt_content->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Your Basket</title>
</head>
<body>
    <h1>Your Basket</h1>
    
    <?php if ($result_content->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0;
                while ($row = $result_content->fetch_assoc()):
                    $total_item_price = $row['price'] * $row['quantity'];
                    $total_price += $total_item_price;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <form method="post" action="basket.php">
                                <input type="hidden" name="content_id" value="<?php echo $row['content_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="0">
                                <button type="submit" name="update_quantity">Update</button>
                            </form>
                        </td>
                        <td><?php echo number_format($total_item_price, 2); ?></td>
                        <td>
                            <form method="post" action="basket.php">
                                <input type="hidden" name="content_id" value="<?php echo $row['content_id']; ?>">
                                <input type="hidden" name="quantity" value="0">
                                <button type="submit" name="update_quantity">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h2>Total Price: <?php echo number_format($total_price, 2); ?></h2>
        <a href="checkout.php">Proceed to Checkout</a>
    <?php else: ?>
        <p>Your basket is empty.</p>
    <?php endif; ?>

</body>
</html>

<?php
##close dbs connection
$conn->close();
?>


