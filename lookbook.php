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
      <div style="background: linear-gradient(180deg, #424242 0%, #0C0C0C 100%); border: 1px solid rgba(255,255,255,0.08); border-radius: 22px; padding: 12px 12px 18px 12px; overflow: hidden; position: relative; box-shadow: 0 12px 30px rgba(0,0,0,0.85);">
        <div style="aspect-ratio: 4/5; background: #000; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
          <?php if (file_exists(__DIR__ . '/assets/images/poster_wear_yourself.jpg')): ?>
            <img src="assets/images/poster_wear_yourself.jpg" alt="Lookbook Shot 1" style="width:100%; height:100%; object-fit:cover; border-radius: 16px;">
            <div class="card-photo-watermark">Jopesh</div>
          <?php else: ?>
            <div class="art-placeholder">
              <div class="art-placeholder-text">Look 01 &bull; 1 of 1</div>
            </div>
          <?php endif; ?>
        </div>
        <div style="padding: 1rem 0.5rem 0.2rem; text-align: center;">
          <span style="font-family: 'Lexend', sans-serif; font-size: 0.72rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-red); font-weight: 500;">Look 01</span>
          <h3 style="font-family: 'Lexend', sans-serif; font-weight: 300; font-size: 1.15rem; color: #fff; margin: 0.4rem 0;">Bloodline Marionette Silhouette</h3>
          <p style="font-family: 'Lexend', sans-serif; font-weight: 300; font-size: 0.85rem; color: #848585;">Deconstructed tailored outerwear paired with raw crimson contrast stitching.</p>
        </div>
      </div>

      <div style="background: linear-gradient(180deg, #424242 0%, #0C0C0C 100%); border: 1px solid rgba(255,255,255,0.08); border-radius: 22px; padding: 12px 12px 18px 12px; overflow: hidden; position: relative; box-shadow: 0 12px 30px rgba(0,0,0,0.85);">
        <div style="aspect-ratio: 4/5; background: #000; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
          <?php if (file_exists(__DIR__ . '/assets/images/jopesh header.jpg')): ?>
            <img src="assets/images/jopesh header.jpg" alt="Lookbook Shot 2" style="width:100%; height:100%; object-fit:cover; border-radius: 16px;">
            <div class="card-photo-watermark">Jopesh</div>
          <?php else: ?>
            <div class="art-placeholder">
              <div class="art-placeholder-text">Look 02 &bull; 1 of 1</div>
            </div>
          <?php endif; ?>
        </div>
        <div style="padding: 1rem 0.5rem 0.2rem; text-align: center;">
          <span style="font-family: 'Lexend', sans-serif; font-size: 0.72rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-red); font-weight: 500;">Look 02</span>
          <h3 style="font-family: 'Lexend', sans-serif; font-weight: 300; font-size: 1.15rem; color: #fff; margin: 0.4rem 0;">Gothic Cross Flared Denim</h3>
          <p style="font-family: 'Lexend', sans-serif; font-weight: 300; font-size: 0.85rem; color: #848585;">High-contrast flared silhouette crafted from heavyweight Japanese raw salvage denim.</p>
        </div>
      </div>

      <div style="background: linear-gradient(180deg, #424242 0%, #0C0C0C 100%); border: 1px solid rgba(255,255,255,0.08); border-radius: 22px; padding: 12px 12px 18px 12px; overflow: hidden; position: relative; box-shadow: 0 12px 30px rgba(0,0,0,0.85);">
        <div style="aspect-ratio: 4/5; background: #000; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
          <div class="art-placeholder">
            <div class="art-placeholder-text">Look 03 &bull; 1 of 1</div>
            <div style="font-size: 0.8rem; color: #fff; font-weight:600; margin-top:8px;">Decay & Hardware Vest</div>
          </div>
        </div>
        <div style="padding: 1rem 0.5rem 0.2rem; text-align: center;">
          <span style="font-family: 'Lexend', sans-serif; font-size: 0.72rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent-red); font-weight: 500;">Look 03</span>
          <h3 style="font-family: 'Lexend', sans-serif; font-weight: 300; font-size: 1.15rem; color: #fff; margin: 0.4rem 0;">Industrial Asymmetrical Kimono</h3>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
