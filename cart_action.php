<?php
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Check login requirement for purchase actions
if (in_array($action, ['add', 'checkout'])) {
    if (!isLoggedIn()) {
        echo json_encode([
            'success' => false,
            'login_required' => true,
            'message' => 'Please log in or create an account to purchase 1-of-1 wearable art pieces.'
        ]);
        exit;
    }
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'add') {
    $productId = (int)($_POST['product_id'] ?? 0);

    $stmt = mysqli_prepare($conn, "SELECT id, name, price, image_url, status, size FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($res);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    if ($product['status'] !== 'available') {
        echo json_encode(['success' => false, 'message' => 'This piece is already ' . $product['status'] . '.']);
        exit;
    }

    // Since pieces are 1-of-1, add only once
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => (float)$product['price'],
            'image_url' => $product['image_url'],
            'size' => $product['size']
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => htmlspecialchars($product['name']) . ' added to your bag.',
        'cart_count' => count($_SESSION['cart']),
        'cart' => array_values($_SESSION['cart'])
    ]);
    exit;
}

if ($action === 'remove') {
    $productId = (int)($_POST['product_id'] ?? 0);
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Piece removed from bag.',
        'cart_count' => count($_SESSION['cart']),
        'cart' => array_values($_SESSION['cart'])
    ]);
    exit;
}

if ($action === 'get') {
    $items = array_values($_SESSION['cart']);
    $total = 0;
    foreach ($items as $item) {
        $total += (float)$item['price'];
    }

    echo json_encode([
        'success' => true,
        'cart_count' => count($items),
        'items' => $items,
        'total' => $total,
        'formatted_total' => '₱' . number_format($total, 2)
    ]);
    exit;
}

if ($action === 'checkout') {
    if (empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Your bag is empty.']);
        exit;
    }

    $shippingAddress = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'GCash / Bank Transfer');

    if (empty($shippingAddress) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Please provide shipping address and contact number.']);
        exit;
    }

    $userId = (int)$_SESSION['user_id'];
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += (float)$item['price'];
    }

    // Insert order
    $orderStmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, shipping_address, phone, order_notes, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
    mysqli_stmt_bind_param($orderStmt, "idssss", $userId, $total, $shippingAddress, $phone, $notes, $paymentMethod);

    if (mysqli_stmt_execute($orderStmt)) {
        $orderId = mysqli_insert_id($conn);

        // Insert items and mark product as sold
        foreach ($_SESSION['cart'] as $productId => $item) {
            $itemStmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, price) VALUES (?, ?, ?)");
            $pPrice = (float)$item['price'];
            mysqli_stmt_bind_param($itemStmt, "iid", $orderId, $productId, $pPrice);
            mysqli_stmt_execute($itemStmt);

            // Mark piece as sold (1 of 1 uniqueness)
            mysqli_query($conn, "UPDATE products SET status = 'sold' WHERE id = $productId");
        }

        // Clear cart
        $_SESSION['cart'] = [];

        echo json_encode([
            'success' => true,
            'message' => 'Order #' . $orderId . ' successfully placed! Jopesh studio will reach out via ' . htmlspecialchars($phone) . ' for wearable art dispatch.',
            'order_id' => $orderId
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create order. Please try again.']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid cart action.']);
exit;
?>
