<?php
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'login_required' => true,
        'message' => 'You must be logged in to place a bid on 1-of-1 wearable art pieces.'
    ]);
    exit;
}

$auctionId = isset($_POST['auction_id']) ? (int)$_POST['auction_id'] : 0;
$bidAmount = isset($_POST['bid_amount']) ? (float)$_POST['bid_amount'] : 0;
$userId = (int)$_SESSION['user_id'];

if ($auctionId <= 0 || $bidAmount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid bid parameters.']);
    exit;
}

// Fetch auction
$stmt = mysqli_prepare($conn, "SELECT a.*, p.name as product_name FROM auctions a JOIN products p ON a.product_id = p.id WHERE a.id = ? AND a.status = 'active'");
mysqli_stmt_bind_param($stmt, "i", $auctionId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$auction = mysqli_fetch_assoc($res);

if (!$auction) {
    echo json_encode(['success' => false, 'message' => 'Auction not found or has ended.']);
    exit;
}

// Check time
if (strtotime($auction['end_time']) <= time()) {
    mysqli_query($conn, "UPDATE auctions SET status = 'ended' WHERE id = $auctionId");
    echo json_encode(['success' => false, 'message' => 'This auction has ended.']);
    exit;
}

$minBid = (float)$auction['current_bid'] + (float)$auction['bid_increment'];
if ($bidAmount < $minBid) {
    echo json_encode([
        'success' => false,
        'message' => 'Minimum bid must be at least ₱' . number_format($minBid, 2)
    ]);
    exit;
}

// Insert bid
$bidStmt = mysqli_prepare($conn, "INSERT INTO bids (auction_id, user_id, bid_amount) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($bidStmt, "iid", $auctionId, $userId, $bidAmount);

if (mysqli_stmt_execute($bidStmt)) {
    // Update auction current_bid
    $updateStmt = mysqli_prepare($conn, "UPDATE auctions SET current_bid = ? WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, "di", $bidAmount, $auctionId);
    mysqli_stmt_execute($updateStmt);

    // Get previous bids
    $historyStmt = mysqli_prepare($conn, "SELECT bid_amount FROM bids WHERE auction_id = ? ORDER BY id DESC LIMIT 3");
    mysqli_stmt_bind_param($historyStmt, "i", $auctionId);
    mysqli_stmt_execute($historyStmt);
    $historyRes = mysqli_stmt_get_result($historyStmt);
    $bids = [];
    while ($row = mysqli_fetch_assoc($historyRes)) {
        $bids[] = (float)$row['bid_amount'];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Your bid of ₱' . number_format($bidAmount, 2) . ' was placed successfully!',
        'current_bid' => $bidAmount,
        'prev_bids' => array_slice($bids, 1, 2)
    ]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place bid. Please try again.']);
    exit;
}
?>
