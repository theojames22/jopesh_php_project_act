<?php
$pageTitle = "Home";
require_once __DIR__ . '/includes/header.php';

// 1. Fetch Collections for Section 2 (Posters) & Section 6 (Collections)
$colResult = mysqli_query($conn, "SELECT * FROM collections ORDER BY sort_order ASC, release_date DESC");
$collections = [];
$latestCollection = null;
while ($col = mysqli_fetch_assoc($colResult)) {
    $collections[] = $col;
    if ($col['is_latest'] == 1 && !$latestCollection) {
        $latestCollection = $col;
    }
}
if (!$latestCollection && count($collections) > 0) {
    $latestCollection = $collections[0];
}

// 2. Fetch Latest Collection Items for Section 3 ("Jopesh's Piece of Art" 3D Sphere)
$latestColId = $latestCollection ? (int)$latestCollection['id'] : 1;
$sphereCards = [];

// Get all products from Nocturne
$pQuery = mysqli_query($conn, "SELECT * FROM products WHERE collection_id = $latestColId ORDER BY id ASC");
$nocturneProducts = [];
$productsByName = [];
while ($row = mysqli_fetch_assoc($pQuery)) {
    $nocturneProducts[] = $row;
    $productsByName[strtolower(trim($row['name']))] = $row;
}

$collectionsDataFile = __DIR__ . '/collections_data.json';
if (file_exists($collectionsDataFile)) {
    $colJson = json_decode(file_get_contents($collectionsDataFile), true);
    if (!empty($colJson['nocturne']['pieces'])) {
        $pieces = $colJson['nocturne']['pieces'];

        // 1. First pass: Front / Primary image of each piece
        foreach ($pieces as $piece) {
            $pName = $piece['name'];
            $matched = null;
            foreach ($productsByName as $k => $prod) {
                if (stripos($k, strtolower($pName)) !== false || stripos(strtolower($pName), $k) !== false) {
                    $matched = $prod;
                    break;
                }
            }

            $sphereCards[] = [
                'id' => $matched ? (int)$matched['id'] : 1,
                'name' => $pName,
                'price' => $matched ? (float)$matched['price'] : 3800.00,
                'image' => jopesh_asset_url($piece['primary_image']),
                'description' => !empty($piece['description']) ? $piece['description'] : ($matched ? $matched['description'] : '1 of 1 Wearable Art'),
                'size' => $matched ? $matched['size'] : '1 of 1 (Custom Fit)',
                'status' => $matched ? $matched['status'] : 'available'
            ];
        }

        // 2. Second pass: Back or detailed image of each piece
        foreach ($pieces as $piece) {
            $pName = $piece['name'];
            $matched = null;
            foreach ($productsByName as $k => $prod) {
                if (stripos($k, strtolower($pName)) !== false || stripos(strtolower($pName), $k) !== false) {
                    $matched = $prod;
                    break;
                }
            }

            $secondImg = '';
            foreach ($piece['images'] as $img) {
                if ($img !== $piece['primary_image'] && (stripos($img, 'back') !== false || stripos($img, 'detailed 2') !== false || stripos($img, 'sample') !== false)) {
                    $secondImg = $img;
                    break;
                }
            }
            if (!$secondImg && count($piece['images']) > 1) {
                $secondImg = $piece['images'][1];
            }
            if ($secondImg) {
                $sphereCards[] = [
                    'id' => $matched ? (int)$matched['id'] : 1,
                    'name' => $pName . ' (Detail)',
                    'price' => $matched ? (float)$matched['price'] : 3800.00,
                    'image' => jopesh_asset_url($secondImg),
                    'description' => !empty($piece['description']) ? $piece['description'] : ($matched ? $matched['description'] : '1 of 1 Wearable Art'),
                    'size' => $matched ? $matched['size'] : '1 of 1 (Custom Fit)',
                    'status' => $matched ? $matched['status'] : 'available'
                ];
            }
        }

        // 3. Highlight detail shots to complete dense 24-card Fibonacci sphere
        $heroExtras = [
            'assets/images/jopesh collections/nocturne collection/𝐓𝐡𝐞 𝐃𝐚𝐠𝐠𝐞𝐫𝐥𝐢𝐧𝐞/the Daggerline back.jpg' => 'The Daggerline (Back Silhouette)',
            'assets/images/jopesh collections/nocturne collection/𝐓𝐡𝐞 𝐒𝐩𝐞𝐜𝐭𝐫𝐞/back.jpg' => 'The Spectre (Back Silhouette)'
        ];
        foreach ($heroExtras as $extraPath => $extraName) {
            if (count($sphereCards) < 24) {
                $sphereCards[] = [
                    'id' => 1,
                    'name' => $extraName,
                    'price' => 4500.00,
                    'image' => jopesh_asset_url($extraPath),
                    'description' => 'Detailed 1 of 1 reworked craftsmanship.',
                    'size' => 'Medium - Large',
                    'status' => 'auction'
                ];
            }
        }
    }
}

// Fallback safety if collections_data.json wasn't loaded
if (count($sphereCards) === 0) {
    $nocturneDir = __DIR__ . '/assets/images/jopesh collections/nocturne collection';
    if (is_dir($nocturneDir)) {
        foreach (scandir($nocturneDir) as $sd) {
            if ($sd === '.' || $sd === '..') continue;
            $dirPath = $nocturneDir . '/' . $sd;
            if (is_dir($dirPath)) {
                foreach (scandir($dirPath) as $f) {
                    if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)) {
                        $sphereCards[] = [
                            'id' => 1,
                            'name' => 'Nocturne Piece',
                            'price' => 3800.00,
                            'image' => jopesh_asset_url('assets/images/jopesh collections/nocturne collection/' . $sd . '/' . $f),
                            'description' => '1 of 1 Wearable Art',
                            'size' => '1 of 1',
                            'status' => 'available'
                        ];
                    }
                }
            }
        }
    }
}

$sphereCards = array_slice($sphereCards, 0, 24);

// 3. Fetch Active Auctions for Section 4
$auctionsQuery = "
    SELECT a.*, p.name as art_name, p.image_url, p.description as art_desc 
    FROM auctions a 
    JOIN products p ON a.product_id = p.id 
    WHERE a.status = 'active' 
    ORDER BY a.end_time ASC
";
$auctionsResult = mysqli_query($conn, $auctionsQuery);
$auctions = [];
while ($auc = mysqli_fetch_assoc($auctionsResult)) {
    $bidStmt = mysqli_prepare($conn, "SELECT bid_amount FROM bids WHERE auction_id = ? ORDER BY id DESC LIMIT 3");
    mysqli_stmt_bind_param($bidStmt, "i", $auc['id']);
    mysqli_stmt_execute($bidStmt);
    $bidRes = mysqli_stmt_get_result($bidStmt);
    $allBids = [];
    while ($bRow = mysqli_fetch_assoc($bidRes)) {
        $allBids[] = (float)$bRow['bid_amount'];
    }
    $auc['highest_bid'] = count($allBids) > 0 ? $allBids[0] : (float)$auc['current_bid'];
    $auc['previous_bids'] = array_slice($allBids, 1, 2);
    $auctions[] = $auc;
}

// 4. Fetch Available Pieces for Section 5 (Layout by fours)
$availResult = mysqli_query($conn, "SELECT * FROM products WHERE status = 'available' ORDER BY id ASC");
$availablePieces = [];
while ($p = mysqli_fetch_assoc($availResult)) {
    $availablePieces[] = $p;
}

// 5. Fetch Sold Pieces for Section 7 (Layout by fours)
$soldResult = mysqli_query($conn, "SELECT * FROM products WHERE status = 'sold' ORDER BY id ASC");
$soldPieces = [];
while ($p = mysqli_fetch_assoc($soldResult)) {
    $soldPieces[] = $p;
}
?>

<!-- Pass real Nocturne collection sphere items to JavaScript -->
<script>
  window.JOPESH_SPHERE_ITEMS = <?php echo json_encode($sphereCards, JSON_UNESCAPED_SLASHES); ?>;
</script>

<!-- SECTION 2: POSTERS (Latest to Oldest Carousel) -->
<section class="posters-section" id="posters">
  <div class="container">
    <div class="posters-slider-container">
      <div class="posters-track" id="posters-track">
        <?php foreach ($collections as $index => $col): 
            $colPageUrl = "collection.php?slug=" . urlencode($col['slug']);
            $posterImage = !empty($col['poster_image']) ? jopesh_asset_url($col['poster_image']) : '';
        ?>
          <div class="poster-slide" data-target-url="<?php echo $colPageUrl; ?>">
            <div class="poster-card">
              <?php if (!empty($posterImage)): ?>
                <img src="<?php echo htmlspecialchars($posterImage); ?>" alt="<?php echo htmlspecialchars($col['title']); ?>" class="poster-img">
              <?php else: ?>
                <div class="art-placeholder" style="height: 100%; border-radius: 12px;">
                  <span class="section-tag" style="margin-bottom: 8px;"><?php echo htmlspecialchars($col['code']); ?></span>
                  <div style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #fff; text-transform: uppercase;">
                    <?php echo htmlspecialchars($col['title']); ?>
                  </div>
                  <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 10px;">
                    Tap to enter collection showcase
                  </div>
                </div>
              <?php endif; ?>

              <div class="poster-overlay">
                <div class="poster-tag"><?php echo htmlspecialchars($col['code']); ?> &bull; <?php echo htmlspecialchars($col['release_date']); ?></div>
                <h2 class="poster-title"><?php echo htmlspecialchars($col['title']); ?></h2>
                <div class="poster-action-hint">
                  <span>Explore Collection</span>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Carousel Arrows < > -->
      <button class="slider-btn prev" id="poster-prev" aria-label="Previous Poster">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </button>
      <button class="slider-btn next" id="poster-next" aria-label="Next Poster">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
      </button>

      <!-- Dots Indicators -->
      <div class="slider-dots" id="poster-dots"></div>
    </div>
  </div>
</section>

<!-- SECTION 3: MALUPIT NA BOLA ("Jopesh's Piece of Art" 3D SPHERE) -->
<section class="gallery-container" id="latest-collection-sphere">
  <div class="sphere-section-header">
    <span class="section-tag"><?php echo htmlspecialchars($latestCollection ? $latestCollection['code'] : 'LATEST COLLECTION'); ?> &bull; 1 OF 1</span>
    <h2 class="section-title">Jopesh's Piece of Art</h2>
    <p class="section-subtitle">
      Scroll to rotate &bull; Drag to spin in 3D &bull; Click any piece to inspect
    </p>
  </div>

  <div class="scene">
    <div class="sphere" id="sphere"></div>
  </div>

  <svg class="network-lines" viewBox="0 0 100 100" preserveAspectRatio="none">
    <path d="M0,50 Q25,30 50,50 T100,50" stroke="rgba(255,255,255,0.06)" stroke-width="0.3" fill="none" />
    <path d="M20,0 L80,100" stroke="rgba(255,255,255,0.04)" stroke-width="0.3" fill="none" />
  </svg>
</section>

<!-- SECTION 4: ACTIVE BIDDING / AUCTIONS -->
<section class="section-padding auctions-section" id="auctions">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Live Collector Auctions</span>
      <h2 class="section-title">Active Bidding</h2>
      <p class="section-subtitle">
        Acquire museum-grade 1-of-1 singular prototypes before the hammer falls.
      </p>
    </div>

    <div class="auctions-grid">
      <?php if (empty($auctions)): ?>
        <div style="grid-column: 1/-1; text-align: center; color: var(--text-faint); padding: 3rem;">
          No active auctions right now. Check back soon for the next private drop.
        </div>
      <?php else: ?>
        <?php foreach ($auctions as $auc): ?>
          <div class="auction-card" data-auction-id="<?php echo $auc['id']; ?>">
            <div class="auction-card-content">
              <!-- Thumbnail Photo -->
              <div class="auction-thumbnail">
                <?php if (!empty($auc['image_url'])): ?>
                  <img src="<?php echo htmlspecialchars(jopesh_asset_url($auc['image_url'])); ?>" alt="<?php echo htmlspecialchars($auc['art_name']); ?>">
                <?php else: ?>
                  <div class="art-placeholder" style="height: 100%;">
                    <div class="art-placeholder-text">1 of 1</div>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Info & Bids -->
              <div class="auction-info">
                <div>
                  <div class="auction-tag">Live Auction</div>
                  <h3 class="auction-art-name"><?php echo htmlspecialchars($auc['art_name']); ?></h3>
                </div>

                <div class="auction-bids-preview">
                  <div class="highest-bid-label">Highest Bid</div>
                  <!-- Highest Bid (bold, at the top) -->
                  <div class="highest-bid-value">₱<?php echo number_format($auc['highest_bid'], 0); ?></div>

                  <!-- Two previous bids (smaller, below highest bid) -->
                  <div class="previous-bids-list">
                    <?php if (!empty($auc['previous_bids'])): ?>
                      <?php foreach ($auc['previous_bids'] as $prevBid): ?>
                        <div class="previous-bid-item">₱<?php echo number_format($prevBid, 0); ?></div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="previous-bid-item" style="text-decoration:none;">Starting: ₱<?php echo number_format($auc['starting_bid'], 0); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Auction Card Footer (Countdown & Place Bid) -->
            <div class="auction-footer">
              <div class="countdown-box">
                <span class="countdown-label">Auction Ends In</span>
                <span class="countdown-timer" data-end-time="<?php echo $auc['end_time']; ?>">Loading...</span>
              </div>

              <button class="btn-bid btn-bid-trigger"
                      data-auction-id="<?php echo $auc['id']; ?>"
                      data-piece-name="<?php echo htmlspecialchars($auc['art_name']); ?>"
                      data-current-bid="<?php echo $auc['highest_bid']; ?>"
                      data-increment="<?php echo $auc['bid_increment']; ?>">
                Place Bid
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- SECTION 5: AVAILABLE PIECES (LAYOUT BY FOURS) -->
<section class="section-padding available-section" id="available-pieces">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Direct Acquisition</span>
      <h2 class="section-title">Available Pieces</h2>
      <p class="section-subtitle">
        Curated 1-of-1 wearable art ready for immediate delivery. Each piece is unique.
      </p>
    </div>

    <!-- Layout by Fours Grid -->
    <div class="grid-by-fours">
      <?php if (empty($availablePieces)): ?>
        <div style="grid-column: 1/-1; text-align: center; color: var(--text-faint); padding: 3rem;">
          All current release pieces have been claimed. Explore archival or upcoming drops.
        </div>
      <?php else: ?>
        <?php foreach ($availablePieces as $piece): ?>
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

              <!-- Buy / Add to Cart Button (Requires Login to buy!) -->
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
</section>

<!-- SECTION 6: COLLECTION SECTION (Directs to Collection Pages) -->
<section class="section-padding collections-section" id="collections">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Archives & Releases</span>
      <h2 class="section-title">The Collections</h2>
      <p class="section-subtitle">
        Select a collection banner to view its full catalog of bespoke 1-of-1 wearable art.
      </p>
    </div>

    <div class="collections-grid">
      <?php 
      $cardClasses = ['col-card-iv', 'col-card-iii', 'col-card-ii', 'col-card-i'];
      foreach ($collections as $idx => $col): 
          $colClass = $cardClasses[$idx % count($cardClasses)];
          $targetUrl = "collection.php?slug=" . urlencode($col['slug']);
      ?>
        <a href="<?php echo $targetUrl; ?>" class="collection-banner-card <?php echo $colClass; ?>">
          <div class="collection-code-tag"><?php echo htmlspecialchars($col['code']); ?> &bull; <?php echo htmlspecialchars($col['release_date']); ?></div>
          <h3 class="collection-banner-title"><?php echo htmlspecialchars($col['title']); ?></h3>
          <p class="collection-banner-desc"><?php echo htmlspecialchars($col['description']); ?></p>
          <div class="collection-enter-btn">
            <span>Enter Collection</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SECTION 7: SOLD PIECES (LAYOUT BY FOURS + MINIMAL SOLD BADGE) -->
<section class="section-padding sold-section" id="sold-pieces">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Archived Pieces</span>
      <h2 class="section-title">Sold Pieces</h2>
      <p class="section-subtitle">
        Historical 1-of-1 creations that now belong to private collectors worldwide.
      </p>
    </div>

    <!-- Layout by Fours Grid -->
    <div class="grid-by-fours">
      <?php if (empty($soldPieces)): ?>
        <div style="grid-column: 1/-1; text-align: center; color: var(--text-faint); padding: 3rem;">
          No sold pieces in archive yet.
        </div>
      <?php else: ?>
        <?php foreach ($soldPieces as $piece): ?>
          <div class="product-card sold-card">
            <!-- Minimal "SOLD" badge at corner as requested in sketch -->
            <span class="sold-corner-badge">SOLD</span>

            <div class="product-media">
              <?php if (!empty($piece['image_url'])): ?>
                <img src="<?php echo htmlspecialchars(jopesh_asset_url($piece['image_url'])); ?>" alt="<?php echo htmlspecialchars($piece['name']); ?>" loading="lazy">
                <div class="card-photo-watermark">Jopesh</div>
              <?php else: ?>
                <div class="art-placeholder">
                  <div class="art-placeholder-text">Archived 1/1</div>
                  <div style="font-size:0.75rem; color:#fff; font-weight:700; margin-top:6px;"><?php echo htmlspecialchars($piece['name']); ?></div>
                </div>
              <?php endif; ?>
            </div>

            <div class="product-content">
              <h3 class="product-title"><?php echo htmlspecialchars($piece['name']); ?></h3>
              <div class="product-meta">
                <span class="product-price" style="color: var(--text-muted); font-size: 0.95rem;">₱<?php echo number_format($piece['price'], 0); ?></span>
                <span class="product-size-badge"><?php echo htmlspecialchars($piece['size']); ?></span>
              </div>
              <div class="sold-archived-label">In Private Collection</div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
