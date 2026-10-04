<?php
$pageTitle = "In Stock";
require_once __DIR__ . '/includes/header.php';

$res = mysqli_query($conn, "SELECT p.*, c.title as collection_title FROM products p LEFT JOIN collections c ON p.collection_id = c.id WHERE p.status = 'available' ORDER BY p.id DESC");
$pieces = [];
while ($p = mysqli_fetch_assoc($res)) {
    $pieces[] = $p;
}
?>

<div style="padding-top: 100px;">
  <div class="container section-padding">
    <div class="section-header">
      <span class="section-tag">Direct Catalog</span>
      <h1 class="section-title">In Stock Pieces</h1>
      <p class="section-subtitle">
        Authentic 1-of-1 handmade wearable art available for direct acquisition.
      </p>
    </div>

    <div class="grid-by-fours">
      <?php if (empty($pieces)): ?>
        <div style="grid-column: 1/-1; text-align: center; color: var(--text-faint); padding: 4rem;">
          No pieces currently in stock. Check our live auctions or upcoming drops.
        </div>
      <?php else: ?>
        <?php foreach ($pieces as $piece): ?>
          <div class="product-card">
            <div class="product-media">
              <?php if (!empty($piece['image_url'])): ?>
                <img src="<?php echo htmlspecialchars(jopesh_asset_url($piece['image_url'])); ?>" alt="<?php echo htmlspecialchars($piece['name']); ?>" loading="lazy">
                <div class="card-photo-watermark">Jopesh</div>
              <?php else: ?>
                <div class="art-placeholder">
                  <div class="art-placeholder-text">1 of 1 Wearable Art</div>
                  <div style="font-size:0.75rem; color:#fff; font-weight:700; margin-top:6px;"><?php echo htmlspecialchars($piece['name']); ?></div>
                </div>
              <?php endif; ?>
            </div>

            <div class="product-content">
              <h3 class="product-title"><?php echo htmlspecialchars($piece['name']); ?></h3>
              <div class="product-meta">
                <span class="product-price">₱<?php echo number_format($piece['price'], 0); ?></span>
                <span class="product-size-badge"><?php echo htmlspecialchars($piece['size']); ?></span>
              </div>

              <button class="btn-buy btn-buy-trigger" data-product-id="<?php echo $piece['id']; ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <span>Acquire Piece</span>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
