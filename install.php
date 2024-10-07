<?php
include_once("connection.php");

try {
    // Create table Products
    $stmt = $conn->prepare("CREATE TABLE IF NOT EXISTS Products (
        product_id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        stock_quantity INT(11) NOT NULL,
        category_id INT(11),
        image_url VARCHAR(255),
        FOREIGN KEY (category_id) REFERENCES Categories(category_id)
    )");

    $stmt->execute();
    echo "Table Products created successfully.";

    // Close the statement
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
