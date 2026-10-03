/**
 * Jopesh Wearable Art - Main Application Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  initMenuDrawer();
  initPostersSlider();
  initCountdowns();
  initAuthModal();
  initCartDrawer();
  initBidding();
  initPurchaseButtons();
});

// Toast notification helper
function showToast(message, type = 'info') {
  let toast = document.getElementById('jopesh-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'jopesh-toast';
    toast.style.cssText = `
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: #15151a;
      border: 1px solid rgba(255,255,255,0.15);
      border-left: 4px solid var(--accent-red);
      color: #fff;
      padding: 14px 20px;
      border-radius: 8px;
      font-size: 0.88rem;
      letter-spacing: 0.04em;
      box-shadow: 0 10px 30px rgba(0,0,0,0.8);
      z-index: 9999;
      transform: translateY(100px);
      opacity: 0;
      transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
      max-width: 380px;
    `;
    document.body.appendChild(toast);
  }

  if (type === 'error') {
    toast.style.borderLeftColor = '#ef4444';
  } else if (type === 'success') {
    toast.style.borderLeftColor = '#22c55e';
  } else {
    toast.style.borderLeftColor = 'var(--accent-red)';
  }

  toast.innerHTML = message;
  toast.style.transform = 'translateY(0)';
  toast.style.opacity = '1';

  clearTimeout(toast.hideTimeout);
  toast.hideTimeout = setTimeout(() => {
    toast.style.transform = 'translateY(100px)';
    toast.style.opacity = '0';
  }, 4000);
}

// ===================================================
// 1. MENU DRAWER (LEFT SIDE)
// ===================================================
function initMenuDrawer() {
  const trigger = document.getElementById('menu-trigger');
  const overlay = document.getElementById('nav-drawer-overlay');
  const closeBtn = document.getElementById('drawer-close');

  if (!trigger || !overlay) return;

  function openDrawer() {
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  trigger.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeDrawer();
  });
}

// ===================================================
// 2. POSTERS CAROUSEL / SLIDER
// ===================================================
function initPostersSlider() {
  const track = document.getElementById('posters-track');
  const slides = document.querySelectorAll('.poster-slide');
  const prevBtn = document.getElementById('poster-prev');
  const nextBtn = document.getElementById('poster-next');
  const dotsContainer = document.getElementById('poster-dots');

  if (!track || slides.length === 0) return;

  let currentIndex = 0;
  const totalSlides = slides.length;

  // Build dots
  if (dotsContainer) {
    dotsContainer.innerHTML = '';
    slides.forEach((_, i) => {
      const dot = document.createElement('div');
      dot.className = `slider-dot ${i === 0 ? 'active' : ''}`;
      dot.addEventListener('click', () => goToSlide(i));
      dotsContainer.appendChild(dot);
    });
  }

  function updateSlider() {
    track.style.transform = `translateX(-${currentIndex * 100}%)`;
    if (dotsContainer) {
      const dots = dotsContainer.querySelectorAll('.slider-dot');
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentIndex);
      });
    }
  }

  function goToSlide(index) {
    currentIndex = (index + totalSlides) % totalSlides;
    updateSlider();
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));
  }

  // Click on slide directs to corresponding collection page
  slides.forEach((slide) => {
    slide.addEventListener('click', () => {
      const targetUrl = slide.dataset.targetUrl;
      if (targetUrl) {
        window.location.href = targetUrl;
      }
    });
  });

  // Auto advance every 6 seconds
  let slideInterval = setInterval(() => goToSlide(currentIndex + 1), 6000);
  const container = document.querySelector('.posters-slider-container');
  if (container) {
    container.addEventListener('mouseenter', () => clearInterval(slideInterval));
    container.addEventListener('mouseleave', () => {
      slideInterval = setInterval(() => goToSlide(currentIndex + 1), 6000);
    });
  }
}

// ===================================================
// 3. COUNTDOWNS FOR AUCTIONS
// ===================================================
function initCountdowns() {
  const timers = document.querySelectorAll('.countdown-timer');

  timers.forEach((timer) => {
    const endTime = new Date(timer.dataset.endTime).getTime();

    function update() {
      const now = new Date().getTime();
      const distance = endTime - now;

      if (distance < 0) {
        timer.innerText = 'ENDED';
        timer.style.color = '#ef4444';
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      const dStr = days > 0 ? `${days}d ` : '';
      timer.innerText = `${dStr}${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
    }

    update();
    setInterval(update, 1000);
  });
}

// ===================================================
// 4. AUTH MODAL (LOGIN / REGISTER)
// ===================================================
function initAuthModal() {
  const modal = document.getElementById('auth-modal');
  const closeBtn = document.getElementById('auth-modal-close');
  const tabLogin = document.getElementById('tab-btn-login');
  const tabRegister = document.getElementById('tab-btn-register');
  const formLogin = document.getElementById('auth-form-login');
  const formRegister = document.getElementById('auth-form-register');
  const msgBox = document.getElementById('auth-message');

  if (!modal) return;

  window.openAuthModal = function (tab = 'login', notice = '') {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    switchTab(tab);
    if (notice && msgBox) {
      msgBox.className = 'auth-message-box error';
      msgBox.innerText = notice;
    } else if (msgBox) {
      msgBox.style.display = 'none';
    }
  };

  window.closeAuthModal = function () {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  };

  function switchTab(tab) {
    if (tab === 'login') {
      tabLogin.classList.add('active');
      tabRegister.classList.remove('active');
      formLogin.style.display = 'block';
      formRegister.style.display = 'none';
    } else {
      tabRegister.classList.add('active');
      tabLogin.classList.remove('active');
      formRegister.style.display = 'block';
      formLogin.style.display = 'none';
    }
    if (msgBox) msgBox.style.display = 'none';
  }

  if (tabLogin) tabLogin.addEventListener('click', () => switchTab('login'));
  if (tabRegister) tabRegister.addEventListener('click', () => switchTab('register'));
  if (closeBtn) closeBtn.addEventListener('click', closeAuthModal);

  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeAuthModal();
  });

  // Login Submit
  if (formLogin) {
    formLogin.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formLogin);
      formData.append('action', 'login');

      try {
        const res = await fetch('auth_action.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          msgBox.className = 'auth-message-box success';
          msgBox.innerText = data.message;
          setTimeout(() => {
            window.location.reload();
          }, 800);
        } else {
          msgBox.className = 'auth-message-box error';
          msgBox.innerText = data.message;
        }
      } catch (err) {
        msgBox.className = 'auth-message-box error';
        msgBox.innerText = 'Network error during login.';
      }
    });
  }

  // Register Submit
  if (formRegister) {
    formRegister.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formRegister);
      formData.append('action', 'register');

      try {
        const res = await fetch('auth_action.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          msgBox.className = 'auth-message-box success';
          msgBox.innerText = data.message;
          setTimeout(() => {
            window.location.reload();
          }, 800);
        } else {
          msgBox.className = 'auth-message-box error';
          msgBox.innerText = data.message;
        }
      } catch (err) {
        msgBox.className = 'auth-message-box error';
        msgBox.innerText = 'Network error during registration.';
      }
    });
  }
}

// ===================================================
// 5. BUY NOW & ADD TO CART (WITH AUTH VERIFICATION)
// ===================================================
function initPurchaseButtons() {
  document.querySelectorAll('.btn-buy-trigger').forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const productId = btn.dataset.productId;
      const isAuth = window.JOPESH_USER_LOGGED_IN === true;

      // User must be logged in to buy!
      if (!isAuth) {
        window.openAuthModal(
          'login',
          'Authentication Required: Please log in or register before acquiring 1-of-1 wearable art pieces.'
        );
        return;
      }

      // Add to cart via AJAX
      const formData = new FormData();
      formData.append('action', 'add');
      formData.append('product_id', productId);

      try {
        const res = await fetch('cart_action.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.login_required) {
          window.openAuthModal('login', data.message);
          return;
        }

        if (data.success) {
          showToast(data.message, 'success');
          updateCartBadge(data.cart_count);
          if (window.openCartDrawer) window.openCartDrawer();
        } else {
          showToast(data.message, 'error');
        }
      } catch (err) {
        showToast('Error adding piece to bag.', 'error');
      }
    });
  });
}

function updateCartBadge(count) {
  const badge = document.querySelector('.cart-badge');
  if (badge) {
    badge.innerText = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
  }
}

// ===================================================
// 6. CART DRAWER & CHECKOUT
// ===================================================
function initCartDrawer() {
  const cartBtn = document.getElementById('cart-btn');
  const overlay = document.getElementById('cart-overlay');
  const closeBtn = document.getElementById('cart-close');
  const container = document.getElementById('cart-items-container');
  const totalEl = document.getElementById('cart-total-display');
  const checkoutBtn = document.getElementById('checkout-btn');

  if (!cartBtn || !overlay) return;

  window.openCartDrawer = async function () {
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    await refreshCart();
  };

  window.closeCartDrawer = function () {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  };

  cartBtn.addEventListener('click', openCartDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeCartDrawer);
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeCartDrawer();
  });

  async function refreshCart() {
    try {
      const res = await fetch('cart_action.php?action=get');
      const data = await res.json();

      if (data.success && container) {
        updateCartBadge(data.cart_count);
        if (data.items.length === 0) {
          container.innerHTML = `
            <div style="text-align: center; color: var(--text-faint); padding: 3rem 1rem;">
              <p style="font-family: var(--font-display); letter-spacing: 0.15em; text-transform: uppercase; font-size: 0.85rem;">Your bag is empty</p>
              <p style="font-size: 0.8rem; margin-top: 6px;">Discover 1-of-1 wearable art in our active catalog.</p>
            </div>
          `;
          if (totalEl) totalEl.innerText = '₱0.00';
          if (checkoutBtn) checkoutBtn.style.display = 'none';
        } else {
          let html = '';
          data.items.forEach((item) => {
            const imgHtml = item.image_url
              ? `<img src="${item.image_url}" alt="${item.name}">`
              : `<div class="art-placeholder" style="font-size:0.6rem;">1/1</div>`;

            html += `
              <div class="cart-item">
                <div class="cart-item-thumb">${imgHtml}</div>
                <div class="cart-item-details">
                  <div class="cart-item-title">${item.name}</div>
                  <div class="cart-item-price">₱${Number(item.price).toLocaleString()}</div>
                  <div style="font-size: 0.72rem; color: var(--text-muted);">${item.size || '1 of 1'}</div>
                </div>
                <button class="cart-item-remove" onclick="removeCartItem(${item.id})">✕</button>
              </div>
            `;
          });
          container.innerHTML = html;
          if (totalEl) totalEl.innerText = data.formatted_total;
          if (checkoutBtn) checkoutBtn.style.display = 'block';
        }
      }
    } catch (e) {
      console.error(e);
    }
  }

  window.removeCartItem = async function (id) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', id);

    try {
      const res = await fetch('cart_action.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        showToast(data.message);
        refreshCart();
      }
    } catch (e) {
      showToast('Error removing piece', 'error');
    }
  };

  // Checkout modal / trigger
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', () => {
      const checkoutModal = document.getElementById('checkout-modal');
      if (checkoutModal) {
        closeCartDrawer();
        checkoutModal.classList.add('active');
      }
    });
  }

  const checkoutForm = document.getElementById('checkout-form');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(checkoutForm);
      formData.append('action', 'checkout');

      try {
        const res = await fetch('cart_action.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          document.getElementById('checkout-modal').classList.remove('active');
          showToast(data.message, 'success');
          updateCartBadge(0);
          setTimeout(() => window.location.reload(), 2000);
        } else {
          showToast(data.message, 'error');
        }
      } catch (err) {
        showToast('Checkout connection error', 'error');
      }
    });
  }
}

// ===================================================
// 7. ACTIVE BIDDING & AUCTION MODAL
// ===================================================
function initBidding() {
  const bidModal = document.getElementById('bid-modal');
  const bidForm = document.getElementById('bid-form');
  const bidAuctionIdInput = document.getElementById('bid-auction-id');
  const bidMinAmountText = document.getElementById('bid-min-amount-text');
  const bidAmountInput = document.getElementById('bid-amount-input');
  const bidPieceName = document.getElementById('bid-piece-name');

  document.querySelectorAll('.btn-bid-trigger').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const isAuth = window.JOPESH_USER_LOGGED_IN === true;

      // Require login to bid!
      if (!isAuth) {
        window.openAuthModal(
          'login',
          'Authentication Required: Please log in or register before bidding on 1-of-1 wearable art.'
        );
        return;
      }

      const auctionId = btn.dataset.auctionId;
      const pieceName = btn.dataset.pieceName;
      const currentBid = parseFloat(btn.dataset.currentBid || 0);
      const increment = parseFloat(btn.dataset.increment || 100);
      const minBid = currentBid + increment;

      if (bidAuctionIdInput) bidAuctionIdInput.value = auctionId;
      if (bidPieceName) bidPieceName.innerText = pieceName;
      if (bidMinAmountText) bidMinAmountText.innerText = `₱${minBid.toLocaleString()}`;
      if (bidAmountInput) {
        bidAmountInput.value = minBid;
        bidAmountInput.min = minBid;
      }

      if (bidModal) {
        bidModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  const bidModalClose = document.getElementById('bid-modal-close');
  if (bidModalClose && bidModal) {
    bidModalClose.addEventListener('click', () => {
      bidModal.classList.remove('active');
      document.body.style.overflow = '';
    });
    bidModal.addEventListener('click', (e) => {
      if (e.target === bidModal) {
        bidModal.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }

  if (bidForm) {
    bidForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(bidForm);

      try {
        const res = await fetch('bid_action.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.login_required) {
          bidModal.classList.remove('active');
          window.openAuthModal('login', data.message);
          return;
        }

        if (data.success) {
          bidModal.classList.remove('active');
          document.body.style.overflow = '';
          showToast(data.message, 'success');

          // Update UI dynamically on the card
          const auctionId = bidAuctionIdInput.value;
          const card = document.querySelector(`.auction-card[data-auction-id="${auctionId}"]`);
          if (card) {
            const highBidEl = card.querySelector('.highest-bid-value');
            if (highBidEl) highBidEl.innerText = `₱${Number(data.current_bid).toLocaleString()}`;

            const prevBidsEl = card.querySelector('.previous-bids-list');
            if (prevBidsEl && data.prev_bids) {
              prevBidsEl.innerHTML = data.prev_bids
                .map((b) => `<div class="previous-bid-item">₱${Number(b).toLocaleString()}</div>`)
                .join('');
            }
          }
        } else {
          showToast(data.message, 'error');
        }
      } catch (err) {
        showToast('Error placing bid.', 'error');
      }
    });
  }
}
