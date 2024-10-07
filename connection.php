<?php 
#first I set the variables, used to connect to the database.
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "madagascar";

#connection to the db
try { 
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful";
} 
catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>



