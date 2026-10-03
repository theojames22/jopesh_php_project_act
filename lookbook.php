<?php
$pageTitle = "Lookbook";
require_once __DIR__ . '/includes/header.php';
?>

<div style="padding-top: 100px;">
  <div class="container section-padding">
    <div class="section-header">
      <span class="section-tag">Visual Editorial</span>
      <h1 class="section-title">The Jopesh Lookbook</h1>
      <p class="section-subtitle">
        A study in silhouettes, deconstruction, and tactile decay. Photographed on analog and digital formats.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
      <div style="background: #101014; border: 1px solid var(--border-subtle); border-radius: 12px; overflow: hidden; position: relative;">
        <div style="aspect-ratio: 3/4; background: #16161d; display: flex; align-items: center; justify-content: center; position: relative;">
          <?php if (file_exists(__DIR__ . '/assets/images/poster_wear_yourself.jpg')): ?>
            <img src="assets/images/poster_wear_yourself.jpg" alt="Lookbook Shot 1" style="width:100%; height:100%; object-fit:cover;">
          <?php else: ?>
            <div class="art-placeholder">
              <div class="art-placeholder-text">Look 01 &bull; 1 of 1</div>
            </div>
          <?php endif; ?>
        </div>
        <div style="padding: 1.5rem;">
          <span style="font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-red); font-weight:700;">Look 01</span>
          <h3 style="font-family: var(--font-display); font-size: 1.2rem; color: #fff; margin: 0.3rem 0;">Bloodline Marionette Silhouette</h3>
          <p style="font-size: 0.85rem; color: var(--text-muted);">Deconstructed tailored outerwear paired with raw crimson contrast stitching.</p>
        </div>
      </div>

      <div style="background: #101014; border: 1px solid var(--border-subtle); border-radius: 12px; overflow: hidden; position: relative;">
        <div style="aspect-ratio: 3/4; background: #16161d; display: flex; align-items: center; justify-content: center; position: relative;">
          <?php if (file_exists(__DIR__ . '/assets/images/jopesh header.jpg')): ?>
            <img src="assets/images/jopesh header.jpg" alt="Lookbook Shot 2" style="width:100%; height:100%; object-fit:cover;">
          <?php else: ?>
            <div class="art-placeholder">
              <div class="art-placeholder-text">Look 02 &bull; 1 of 1</div>
            </div>
          <?php endif; ?>
        </div>
        <div style="padding: 1.5rem;">
          <span style="font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-red); font-weight:700;">Look 02</span>
          <h3 style="font-family: var(--font-display); font-size: 1.2rem; color: #fff; margin: 0.3rem 0;">Gothic Cross Flared Denim</h3>
          <p style="font-size: 0.85rem; color: var(--text-muted);">High-contrast flared silhouette crafted from heavyweight Japanese raw salvage denim.</p>
        </div>
      </div>

      <div style="background: #101014; border: 1px solid var(--border-subtle); border-radius: 12px; overflow: hidden; position: relative;">
        <div style="aspect-ratio: 3/4; background: #16161d; display: flex; align-items: center; justify-content: center; position: relative;">
          <div class="art-placeholder">
            <div class="art-placeholder-text">Look 03 &bull; 1 of 1</div>
            <div style="font-size: 0.8rem; color: #fff; font-weight:600; margin-top:8px;">Decay & Hardware Vest</div>
          </div>
        </div>
        <div style="padding: 1.5rem;">
          <span style="font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-red); font-weight:700;">Look 03</span>
          <h3 style="font-family: var(--font-display); font-size: 1.2rem; color: #fff; margin: 0.3rem 0;">Industrial Asymmetrical Kimono</h3>
          <p style="font-size: 0.85rem; color: var(--text-muted);">Sculptural canvas structure bound with aged chrome hardware and silver chains.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
