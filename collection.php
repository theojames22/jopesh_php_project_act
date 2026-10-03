<?php
require_once __DIR__ . '/includes/auth.php';

$slug = $_GET['slug'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$collection = null;
if (!empty($slug)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM collections WHERE slug = ?");
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $collection = mysqli_fetch_assoc($res);
} elseif ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM collections WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $collection = mysqli_fetch_assoc($res);
}

if (!$collection) {
    header("Location: index.php#collections");
    exit;
}

$pageTitle = $collection['title'];
require_once __DIR__ . '/includes/header.php';

// Fetch pieces for this collection
$colId = (int)$collection['id'];
$prodStmt = mysqli_prepare($conn, "SELECT * FROM products WHERE collection_id = ? ORDER BY status ASC, id DESC");
mysqli_stmt_bind_param($prodStmt, "i", $colId);
mysqli_stmt_execute($prodStmt);
$prodRes = mysqli_stmt_get_result($prodStmt);
$pieces = [];
while ($p = mysqli_fetch_assoc($prodRes)) {
    $pieces[] = $p;
}
?>

<div style="padding-top: 100px;">
  <div class="container section-padding">
    <div style="margin-bottom: 2rem;">
      <a href="index.php#collections" style="color: var(--text-muted); font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
        &larr; Back to All Collections
      </a>
    </div>

    <div class="section-header" style="text-align: left; max-width: 900px; margin-bottom: 3.5rem;">
      <span class="section-tag"><?php echo htmlspecialchars($collection['code']); ?> &bull; <?php echo htmlspecialchars($collection['release_date']); ?></span>
      <h1 class="section-title"><?php echo htmlspecialchars($collection['title']); ?></h1>
      <p class="section-subtitle" style="margin: 1rem 0 0 0; max-width: 100%;">
        <?php echo nl2br(htmlspecialchars($collection['description'])); ?>
      </p>
    </div>

    <div class="grid-by-fours">
      <?php if (empty($pieces)): ?>
        <div style="grid-column: 1/-1; text-align: center; color: var(--text-faint); padding: 4rem;">
          No pieces currently cataloged for this collection.
        </div>
      <?php else: ?>
        <?php foreach ($pieces as $piece): ?>
          <div class="product-card <?php echo ($piece['status'] === 'sold') ? 'sold-card' : ''; ?>">
            <?php if ($piece['status'] === 'sold'): ?>
              <span class="sold-corner-badge">SOLD</span>
            <?php elseif ($piece['status'] === 'auction'): ?>
              <span class="sold-corner-badge" style="background: var(--accent-red); border-color: var(--accent-red);">AUCTION</span>
            <?php endif; ?>

            <div class="product-media">
              <?php if (!empty($piece['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($piece['image_url']); ?>" alt="<?php echo htmlspecialchars($piece['name']); ?>" loading="lazy">
              <?php else: ?>
                <div class="art-placeholder">
                  <div class="art-placeholder-text">1 of 1 Wearable Art</div>
                  <div style="font-size:0.75rem; color:#fff; font-weight:700; margin-top:6px;"><?php echo htmlspecialchars($piece['name']); ?></div>
                </div>
              <?php endif; ?>
            </div>

            <div class="product-content">
              <h3 class="product-title"><?php echo htmlspecialchars($piece['name']); ?></h3>
              <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($piece['description']); ?></p>
              
              <div class="product-meta">
                <span class="product-price">₱<?php echo number_format($piece['price'], 0); ?></span>
                <span class="product-size-badge"><?php echo htmlspecialchars($piece['size']); ?></span>
              </div>

              <?php if ($piece['status'] === 'available'): ?>
                <button class="btn-buy btn-buy-trigger" data-product-id="<?php echo $piece['id']; ?>">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                  <span>Acquire Piece</span>
                </button>
              <?php elseif ($piece['status'] === 'auction'): ?>
                <a href="index.php#auctions" class="btn-buy" style="background: var(--accent-red); color: #fff; text-align: center; justify-content: center;">
                  View Live Auction
                </a>
              <?php else: ?>
                <div class="sold-archived-label">In Private Collection</div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
