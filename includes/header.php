<?php
require_once __DIR__ . '/auth.php';
$currentUser = getCurrentUser();
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?>JOPESH — 1 of 1 Wearable Art</title>
  <meta name="description" content="Jopesh 1 of 1 Wearable Art. Wear Yourself — Own Yourself. Hand-crafted singular avant-garde streetwear garments and underground creations.">
  <link rel="stylesheet" href="css/style.css">
  <script>
    window.JOPESH_USER_LOGGED_IN = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
  </script>
</head>
<body>
<div class="grid-overlay"></div>

<!-- Left Side Navigation Drawer -->
<div class="nav-drawer-overlay" id="nav-drawer-overlay">
  <div class="nav-drawer">
    <div class="drawer-header">
      <span class="drawer-title">JOPESH</span>
      <button class="drawer-close" id="drawer-close" aria-label="Close navigation">&times;</button>
    </div>

    <nav class="drawer-links">
      <a href="index.php" class="drawer-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a>
      <a href="in-stock.php" class="drawer-link <?php echo (basename($_SERVER['PHP_SELF']) == 'in-stock.php') ? 'active' : ''; ?>">In Stock</a>
      <a href="lookbook.php" class="drawer-link <?php echo (basename($_SERVER['PHP_SELF']) == 'lookbook.php') ? 'active' : ''; ?>">Lookbook</a>
      <a href="faqs.php" class="drawer-link <?php echo (basename($_SERVER['PHP_SELF']) == 'faqs.php') ? 'active' : ''; ?>">FAQs</a>
      <a href="how-to-order.php" class="drawer-link <?php echo (basename($_SERVER['PHP_SELF']) == 'how-to-order.php') ? 'active' : ''; ?>">How to Order</a>
      <a href="size-chart.php" class="drawer-link <?php echo (basename($_SERVER['PHP_SELF']) == 'size-chart.php') ? 'active' : ''; ?>">Size Chart</a>
      <a href="contact.php" class="drawer-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact</a>
    </nav>

    <div class="drawer-footer">
      <?php if (isLoggedIn()): ?>
        <div class="drawer-user-info">
          Signed in as <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>
        </div>
        <a href="auth_action.php?action=logout&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn-buy" style="text-align: center;">Logout</a>
      <?php else: ?>
        <button onclick="closeDrawer(); openAuthModal('login');" class="btn-buy">Log In / Sign Up</button>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- SECTION 1: HEADER -->
<header class="site-header">
  <div class="header-container">
    <!-- Left: Menu Trigger -->
    <button class="menu-trigger" id="menu-trigger" aria-label="Open Menu">
      <div class="menu-icon-bars">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <span>MENU</span>
    </button>

    <!-- Center: Jopesh Text Logo (redirects to Home) -->
    <div class="logo-wrapper">
      <a href="index.php" class="brand-logo" title="Jopesh Home">
        <img src="assets/images/jopesh%20logo.png" alt="Jopesh" class="brand-logo-img">
      </a>
    </div>

    <!-- Right: Account & Cart Actions -->
    <div class="header-actions">
      <?php if (isLoggedIn()): ?>
        <div style="position: relative; display: inline-block;">
          <button class="action-btn" onclick="openUserMenu()" title="Account">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span style="font-size:0.78rem; text-transform:uppercase; font-weight:700;"><?php echo htmlspecialchars($currentUser['username']); ?></span>
          </button>
        </div>
      <?php else: ?>
        <button class="action-btn" onclick="openAuthModal('login')" title="Log In">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          <span style="display:none;" class="login-text-desktop">LOG IN</span>
        </button>
      <?php endif; ?>

      <!-- Cart Button (Optional cart drawer) -->
      <button class="action-btn cart-btn" id="cart-btn" aria-label="Shopping Bag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        <span class="cart-badge" style="<?php echo ($cartCount > 0) ? 'display:flex;' : 'display:none;'; ?>"><?php echo $cartCount; ?></span>
      </button>
    </div>
  </div>
</header>

<!-- Authentication Modal (Login / Register) -->
<div class="modal-backdrop" id="auth-modal">
  <div class="modal-dialog">
    <button class="modal-close-btn" id="auth-modal-close">&times;</button>
    
    <div class="auth-tabs">
      <button class="auth-tab-btn active" id="tab-btn-login">Log In</button>
      <button class="auth-tab-btn" id="tab-btn-register">Register</button>
    </div>

    <div class="auth-message-box" id="auth-message"></div>

    <!-- Login Form -->
    <form id="auth-form-login">
      <div class="auth-form-group">
        <label for="login-identifier">Username or Email</label>
        <input type="text" id="login-identifier" name="identifier" class="auth-input" required autocomplete="username">
      </div>
      <div class="auth-form-group">
        <label for="login-password">Password</label>
        <input type="password" id="login-password" name="password" class="auth-input" required autocomplete="current-password">
      </div>
      <button type="submit" class="auth-btn-submit">Enter Jopesh</button>
      <div style="font-size: 0.75rem; color: var(--text-faint); margin-top: 1rem; text-align: center;">
        Need to bid or purchase? Enter your collector account.
      </div>
    </form>

    <!-- Register Form -->
    <form id="auth-form-register" style="display: none;">
      <div class="auth-form-group">
        <label for="reg-username">Username</label>
        <input type="text" id="reg-username" name="username" class="auth-input" required>
      </div>
      <div class="auth-form-group">
        <label for="reg-email">Email Address</label>
        <input type="email" id="reg-email" name="email" class="auth-input" required>
      </div>
      <div class="auth-form-group">
        <label for="reg-name">Full Name</label>
        <input type="text" id="reg-name" name="full_name" class="auth-input">
      </div>
      <div class="auth-form-group">
        <label for="reg-password">Password (min 6 characters)</label>
        <input type="password" id="reg-password" name="password" class="auth-input" minlength="6" required>
      </div>
      <button type="submit" class="auth-btn-submit">Create Collector Account</button>
    </form>
  </div>
</div>

<!-- Cart Drawer & Overlay -->
<div class="cart-overlay" id="cart-overlay"></div>
<div class="cart-drawer" id="cart-drawer">
  <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--border-subtle);">
    <span style="font-family: var(--font-display); font-size: 1.1rem; letter-spacing: 0.1em; text-transform: uppercase;">Your Bag</span>
    <button id="cart-close" style="background: none; border: none; color: var(--text-muted); font-size: 1.4rem; cursor: pointer;">&times;</button>
  </div>

  <div class="cart-items-list" id="cart-items-container">
    <!-- Populated by JavaScript -->
  </div>

  <div class="cart-footer">
    <div class="cart-total-row">
      <span>Subtotal</span>
      <span id="cart-total-display">₱0.00</span>
    </div>
    <button class="btn-bid" id="checkout-btn" style="width: 100%; text-align: center;">Proceed to Checkout</button>
  </div>
</div>

<!-- Checkout Modal -->
<div class="modal-backdrop" id="checkout-modal">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="document.getElementById('checkout-modal').classList.remove('active')">&times;</button>
    <div style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase;">
      Complete 1-of-1 Order
    </div>
    <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1.5rem;">
      Each Jopesh piece is a singular handmade work of art. Confirm delivery details below:
    </p>

    <form id="checkout-form">
      <div class="auth-form-group">
        <label for="co-phone">Contact / Mobile Number (Required for Dispatch)</label>
        <input type="text" id="co-phone" name="phone" class="auth-input" placeholder="e.g. 0917 123 4567" required>
      </div>
      <div class="auth-form-group">
        <label for="co-address">Complete Shipping Address</label>
        <textarea id="co-address" name="address" class="auth-input" rows="3" placeholder="House/Unit #, Street, Barangay, City, Postal Code" required></textarea>
      </div>
      <div class="auth-form-group">
        <label for="co-payment">Payment Method</label>
        <select id="co-payment" name="payment_method" class="auth-input">
          <option value="GCash">GCash</option>
          <option value="Maya">Maya</option>
          <option value="Bank Transfer (BDO / BPI)">Bank Transfer (BDO / BPI)</option>
          <option value="Cash on Delivery">Cash on Delivery (Metro Manila only)</option>
        </select>
      </div>
      <div class="auth-form-group">
        <label for="co-notes">Special Requests / Sizing Notes (Optional)</label>
        <input type="text" id="co-notes" name="notes" class="auth-input" placeholder="Custom sleeve length, gift box, etc.">
      </div>
      <button type="submit" class="auth-btn-submit">Confirm Acquisition</button>
    </form>
  </div>
</div>

<!-- Active Bid Modal -->
<div class="modal-backdrop" id="bid-modal">
  <div class="modal-dialog">
    <button class="modal-close-btn" id="bid-modal-close">&times;</button>
    <div style="font-family: var(--font-display); font-size: 1.2rem; font-weight: 700; margin-bottom: 0.4rem; text-transform: uppercase;">
      Place Auction Bid
    </div>
    <div id="bid-piece-name" style="font-size: 0.95rem; color: #fff; font-weight: 600; margin-bottom: 1rem;">Piece Name</div>

    <form id="bid-form">
      <input type="hidden" name="auction_id" id="bid-auction-id" value="">
      <div style="margin-bottom: 1.2rem; font-size: 0.85rem; color: var(--text-muted);">
        Minimum acceptable bid: <strong id="bid-min-amount-text" style="color: #fff;">₱0.00</strong>
      </div>
      <div class="auth-form-group">
        <label for="bid-amount-input">Your Bid Amount (₱ PHP)</label>
        <input type="number" id="bid-amount-input" name="bid_amount" class="auth-input" step="50" required>
      </div>
      <button type="submit" class="auth-btn-submit" style="background: var(--accent-red); color: #fff;">Submit Official Bid</button>
      <p style="font-size: 0.72rem; color: var(--text-faint); margin-top: 0.8rem; text-align: center;">
        Bids on 1-of-1 wearable art pieces are binding. Highest bidder will be contacted at auction close.
      </p>
    </form>
  </div>
</div>

<!-- Sphere Card Preview Modal (Clickable Cards on Sphere) -->
<div class="modal-backdrop" id="sphere-card-modal">
  <div class="modal-dialog" style="max-width: 480px; text-align: left;">
    <button class="modal-close-btn" onclick="document.getElementById('sphere-card-modal').classList.remove('active')">&times;</button>
    <div id="sphere-modal-badge" style="font-family: var(--font-display); font-size: 0.75rem; letter-spacing: 0.2em; color: var(--accent-red); text-transform: uppercase; margin-bottom: 0.4rem; font-weight: 700;">
      NOCTURNE COLLECTION &bull; 1 OF 1
    </div>
    <h3 id="sphere-modal-title" style="font-family: var(--font-display); font-size: 1.35rem; color: #fff; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
      Wearable Art Piece
    </h3>
    <div style="width: 100%; aspect-ratio: 4/5; border-radius: 12px; overflow: hidden; background: #121217; margin-bottom: 1rem; border: 1px solid var(--border-subtle); position: relative;">
      <img id="sphere-modal-img" src="" alt="Piece Preview" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
      <span id="sphere-modal-price" style="font-size: 1.2rem; font-weight: 700; color: #fff; font-family: var(--font-display);">₱3,800</span>
      <span id="sphere-modal-size" class="product-size-badge">1 of 1</span>
    </div>
    <p id="sphere-modal-desc" style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.2rem; max-height: 90px; overflow-y: auto;">
      1 of 1 Wearable Art by Jopesh.
    </p>
    <div style="display: flex; gap: 0.8rem; justify-content: stretch;">
      <button id="sphere-modal-action-btn" class="btn-buy" style="flex: 1; justify-content: center; text-align: center;">
        Acquire 1-of-1 Piece
      </button>
      <button class="btn-buy" onclick="document.getElementById('sphere-card-modal').classList.remove('active')" style="background: transparent; border: 1px solid var(--border-subtle); width: auto; padding: 0.7rem 1.2rem;">
        Close
      </button>
    </div>
  </div>
</div>
