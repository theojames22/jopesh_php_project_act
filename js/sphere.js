// ==============================================================================
// JOPESH 3D FIBONACCI ART SPHERE ("Malupit na Bola")
// Renders 24 curated 1-of-1 wearable art cards in full interactive 3D perspective.
// Features:
// - Ambient idle rotation
// - GSAP ScrollTrigger rotation scrub
// - Mouse drag / touch swipe 3D manipulation
// - Clickable preview modal with authentic metadata and direct acquisition flow
// ==============================================================================

(function () {
  'use strict';

  // Fallback images only if dynamic data is completely absent
  const fallbackImages = [
    "assets/images/jopesh header.jpg",
    "assets/images/poster_wear_yourself.jpg"
  ];

  function getSphereData() {
    if (window.JOPESH_SPHERE_ITEMS && Array.isArray(window.JOPESH_SPHERE_ITEMS) && window.JOPESH_SPHERE_ITEMS.length > 0) {
      return window.JOPESH_SPHERE_ITEMS;
    }
    if (window.JOPESH_CUSTOM_SPHERE_IMAGES && Array.isArray(window.JOPESH_CUSTOM_SPHERE_IMAGES) && window.JOPESH_CUSTOM_SPHERE_IMAGES.length > 0) {
      return window.JOPESH_CUSTOM_SPHERE_IMAGES.map((img, i) => ({
        id: i + 1,
        name: `Jopesh Piece #${i + 1}`,
        price: 3800,
        image: img,
        description: '1 of 1 Wearable Art by Jopesh',
        size: '1 of 1',
        status: 'available'
      }));
    }
    return fallbackImages.map((img, i) => ({
      id: i + 1,
      name: `Jopesh Prototype #${i + 1}`,
      price: 3800,
      image: img,
      description: '1 of 1 Wearable Art',
      size: '1 of 1',
      status: 'available'
    }));
  }

  function init3DSphere() {
    const sphere = document.getElementById("sphere");
    const section = document.getElementById("latest-collection-sphere");
    if (!sphere || !section) return;

    const rawData = getSphereData();
    const totalCards = 24;

    if (!Array.isArray(rawData) || rawData.length === 0) return;

    // Build exactly 24 items safely
    const galleryItems = [];
    for (let i = 0; i < totalCards; i++) {
      galleryItems.push(rawData[i % rawData.length]);
    }

    const radius = window.innerWidth < 768 ? 200 : 380;
    sphere.innerHTML = '';
    const allCards = [];

    // State tracking
    let isDragging = false;
    let hasDragged = false;
    let startX = 0;
    let startY = 0;
    let baseRotY = 0;
    let baseRotX = 14;
    let dragRotY = 0;
    let dragRotX = 0;
    let scrollRotY = 0;
    let scrollRotX = 0;
    let ambientRotY = 0;
    let lastInteractionTime = Date.now();

    // 1. Generate Fibonacci Sphere 3D Layout
    galleryItems.forEach((item, i) => {
      const card = document.createElement("div");
      card.classList.add("clay-card");
      card.dataset.index = i;

      const img = document.createElement("img");
      img.src = item.image;
      img.alt = item.name || `Jopesh Piece #${i + 1}`;
      img.loading = "lazy";
      img.draggable = false;
      card.appendChild(img);

      // Fibonacci sphere distribution math
      const phi = Math.acos(1 - (2 * (i + 0.5)) / totalCards);
      const theta = Math.PI * (1 + Math.sqrt(5)) * i;

      const x = radius * Math.cos(theta) * Math.sin(phi);
      const y = radius * Math.sin(theta) * Math.sin(phi);
      const z = radius * Math.cos(phi);

      // Calculate outward rotation
      const cardRotY = Math.atan2(x, z) * (180 / Math.PI);
      const cardRotX = Math.asin(-y / radius) * (180 / Math.PI);

      card.style.transform = `translate3d(${x}px, ${y}px, ${z}px) rotateY(${cardRotY}deg) rotateX(${cardRotX}deg)`;

      // Click handler to open preview modal
      card.addEventListener("click", (e) => {
        e.stopPropagation();
        if (hasDragged) return;

        allCards.forEach(c => c.classList.remove("active-card"));
        card.classList.add("active-card");

        openSphereModal(item);
      });

      sphere.appendChild(card);
      allCards.push(card);
    });

    function openSphereModal(item) {
      const modal = document.getElementById("sphere-card-modal");
      if (!modal) return;

      const modalImg = document.getElementById("sphere-modal-img");
      const modalTitle = document.getElementById("sphere-modal-title");
      const modalPrice = document.getElementById("sphere-modal-price");
      const modalSize = document.getElementById("sphere-modal-size");
      const modalDesc = document.getElementById("sphere-modal-desc");
      const modalBadge = document.getElementById("sphere-modal-badge");
      const modalActionBtn = document.getElementById("sphere-modal-action-btn");

      if (modalImg) modalImg.src = item.image;
      if (modalTitle) modalTitle.textContent = item.name || 'Wearable Art Piece';
      if (modalPrice) modalPrice.textContent = item.price ? `₱${Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}` : '1 of 1';
      if (modalSize) modalSize.textContent = item.size ? `Size: ${item.size}` : '1 of 1';
      if (modalDesc) modalDesc.textContent = item.description || 'Authentic 1-of-1 Wearable Art by Jopesh.';

      if (modalBadge) {
        modalBadge.textContent = (item.status === 'auction')
          ? 'LIVE AUCTION • 1 OF 1'
          : 'NOCTURNE COLLECTION • 1 OF 1';
      }

      if (modalActionBtn) {
        if (item.status === 'auction') {
          modalActionBtn.textContent = 'Bid on This Piece';
          modalActionBtn.onclick = () => {
            modal.classList.remove('active');
            const targetAuc = document.querySelector(`.auction-card[data-auction-id="${item.id}"]`) || document.getElementById('auctions');
            if (targetAuc) {
              targetAuc.scrollIntoView({ behavior: 'smooth' });
            }
          };
        } else {
          modalActionBtn.textContent = 'Acquire 1-of-1 Piece';
          modalActionBtn.onclick = () => {
            modal.classList.remove('active');
            const trigger = document.querySelector(`.btn-buy-trigger[data-product-id="${item.id}"]`);
            if (trigger) {
              trigger.click();
            } else if (window.handleAddToCart) {
              window.handleAddToCart(item.id);
            } else {
              window.location.href = 'in-stock.php';
            }
          };
        }
      }

      modal.classList.add("active");
    }

    function updateTransform() {
      const totalY = (baseRotY + dragRotY + scrollRotY + ambientRotY);
      const totalX = Math.max(-60, Math.min(60, baseRotX + dragRotX + scrollRotX));
      sphere.style.transform = `rotateY(${totalY}deg) rotateX(${totalX}deg)`;
    }

    // Apply immediate transform on load so sphere is visible instantly
    updateTransform();
    if (allCards.length > 0) {
      allCards[0].classList.add("active-card");
    }

    // 2. Ambient Drift Loop (hypnotic idle rotation)
    function ambientTick() {
      if (!isDragging && (Date.now() - lastInteractionTime > 700)) {
        ambientRotY += 0.16;
        updateTransform();
      }
      requestAnimationFrame(ambientTick);
    }
    requestAnimationFrame(ambientTick);

    // 3. GSAP ScrollTrigger Integration
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
      gsap.registerPlugin(ScrollTrigger);

      ScrollTrigger.create({
        trigger: section,
        start: "top bottom",
        end: "bottom top",
        scrub: 1.2,
        onUpdate: (self) => {
          const progress = self.progress;
          scrollRotY = progress * 360 * 2; // 2 smooth 360 rotations
          scrollRotX = Math.sin(progress * Math.PI) * 25; // gentle 3D tilt
          updateTransform();

          // Highlight nearest card facing the camera
          const focusIndex = Math.floor(progress * totalCards) % totalCards;
          allCards.forEach((c, idx) => {
            if (Math.abs(idx - focusIndex) <= 1 || Math.abs(idx - focusIndex) >= totalCards - 1) {
              c.classList.add("active-card");
            } else {
              c.classList.remove("active-card");
            }
          });
        }
      });
    }

    // 4. Smooth Mouse Drag & Touch Swipe (Free 3D rotation)
    const sceneEl = section.querySelector('.scene');
    if (sceneEl) {
      const handlePointerDown = (clientX, clientY) => {
        isDragging = true;
        hasDragged = false;
        startX = clientX;
        startY = clientY;
        lastInteractionTime = Date.now();
        sceneEl.style.cursor = 'grabbing';
      };

      const handlePointerMove = (clientX, clientY) => {
        if (!isDragging) return;
        const dx = clientX - startX;
        const dy = clientY - startY;

        if (Math.abs(dx) > 3 || Math.abs(dy) > 3) {
          hasDragged = true;
        }

        dragRotY += dx * 0.45;
        dragRotX -= dy * 0.35;
        dragRotX = Math.max(-60, Math.min(60, dragRotX));
        startX = clientX;
        startY = clientY;
        lastInteractionTime = Date.now();
        updateTransform();
      };

      const handlePointerUp = () => {
        if (isDragging) {
          isDragging = false;
          lastInteractionTime = Date.now();
          sceneEl.style.cursor = 'grab';
        }
      };

      sceneEl.addEventListener('mousedown', (e) => handlePointerDown(e.clientX, e.clientY));
      window.addEventListener('mousemove', (e) => handlePointerMove(e.clientX, e.clientY));
      window.addEventListener('mouseup', handlePointerUp);

      sceneEl.addEventListener('touchstart', (e) => {
        if (e.touches.length === 1) handlePointerDown(e.touches[0].clientX, e.touches[0].clientY);
      }, { passive: true });

      window.addEventListener('touchmove', (e) => {
        if (isDragging && e.touches.length === 1) handlePointerMove(e.touches[0].clientX, e.touches[0].clientY);
      }, { passive: true });

      window.addEventListener('touchend', handlePointerUp);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init3DSphere);
  } else {
    init3DSphere();
  }
})();
