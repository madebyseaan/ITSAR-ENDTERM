<?php
require_once __DIR__ . '/../config.php'; // Adjust the path to config.php as needed
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_inventory");

// Use $_REQUEST to catch both POST (from forms) and GET (from links)
$action = $_REQUEST['action'] ?? '';

// ADD BOOK
if ($action == 'add') {
    $stmt = $conn->prepare("INSERT INTO books (title, author, price, stock_quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $_POST['title'], $_POST['author'], $_POST['price'], $_POST['stock']);
    $stmt->execute();
    header("Location: ../frontend/inventory_crud.php");
}

// EDIT BOOK
if ($action == 'edit') {
    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, price=?, stock_quantity=? WHERE id=?");
    $stmt->bind_param("ssdii", $_POST['title'], $_POST['author'], $_POST['price'], $_POST['stock'], $_POST['id']);
    $stmt->execute();
    header("Location: ../frontend/inventory_crud.php");
}

// DELETE BOOK
if ($action == 'delete') {
    $conn->query("DELETE FROM books WHERE id = " . $_GET['id']);
    header("Location: ../frontend/inventory_crud.php");
}
?>