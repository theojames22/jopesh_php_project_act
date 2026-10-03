<?php
$pageTitle = "Contact Studio";
require_once __DIR__ . '/includes/header.php';

$sentNotice = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sentNotice = true;
}
?>

<div style="padding-top: 100px;">
  <div class="container section-padding">
    <div class="section-header">
      <span class="section-tag">Direct Atelier Inquiries</span>
      <h1 class="section-title">Contact Jopesh</h1>
      <p class="section-subtitle">
        For private commissions, VIP acquisition consultations, press, and collector support.
      </p>
    </div>

    <div style="max-width: 680px; margin: 0 auto; background: #101014; border: 1px solid var(--border-subtle); border-radius: 14px; padding: 2.5rem;">
      <?php if ($sentNotice): ?>
        <div class="auth-message-box success" style="display: block; margin-bottom: 2rem;">
          ✦ Inscription received. Our atelier team will review your message and reply within 24 hours.
        </div>
      <?php endif; ?>

      <form method="POST" action="contact.php">
        <div class="auth-form-group">
          <label for="c-name">Full Name / Pseudonym</label>
          <input type="text" id="c-name" name="name" class="auth-input" required placeholder="e.g. John Doe">
        </div>

        <div class="auth-form-group">
          <label for="c-email">Email Address</label>
          <input type="email" id="c-email" name="email" class="auth-input" required placeholder="collector@domain.com">
        </div>

        <div class="auth-form-group">
          <label for="c-type">Inquiry Nature</label>
          <select id="c-type" name="type" class="auth-input">
            <option value="acquisition">Piece Acquisition Inquiry</option>
            <option value="auction">Auction / Private Bidding</option>
            <option value="bespoke">Bespoke 1-of-1 Commission</option>
            <option value="press">Styling, Editorial & Press</option>
            <option value="other">General Inquiries</option>
          </select>
        </div>

        <div class="auth-form-group">
          <label for="c-message">Message</label>
          <textarea id="c-message" name="message" class="auth-input" rows="5" required placeholder="Specify piece names, event dates, or measurement specifications..."></textarea>
        </div>

        <button type="submit" class="auth-btn-submit">Transmit Message</button>
      </form>

      <div style="margin-top: 2.5rem; padding-top: 2rem; border-top: 1px solid var(--border-subtle); text-align: center; color: var(--text-faint); font-size: 0.85rem;">
        Direct Inquiries: <a href="mailto:studio@jopesh.com" style="color: #fff; text-decoration: underline;">studio@jopesh.com</a> &bull; Manila Atelier
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
