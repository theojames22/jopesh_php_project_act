/**
 * Jopesh Wearable Art - 3D Sphere ("Malupit na bola")
 * Dynamically renders photos of the latest collection onto a 3D Fibonacci sphere.
 * Flexible: works with ANY number of photos (e.g. 6, 8, 12, 16, 24+).
 * Features mouse/touch drag rotation and smooth continuous spin.
 */

class JopeshSphere {
  constructor(containerId, items) {
    this.container = document.getElementById(containerId);
    if (!this.container) return;

    this.items = items || [];
    this.stage = document.createElement('div');
    this.stage.className = 'sphere-stage';
    this.container.appendChild(this.stage);

    this.radius = window.innerWidth < 768 ? 190 : 340;
    this.cards = [];
    this.rotX = 15;
    this.rotY = 0;
    this.isDragging = false;
    this.startX = 0;
    this.startY = 0;
    this.autoRotateSpeed = 0.25;

    this.init();
  }

  init() {
    this.buildCards();
    this.bindEvents();
    this.animate();
  }

  buildCards() {
    this.stage.innerHTML = '';
    this.cards = [];

    // If items are fewer than 12, duplicate or mirror to create a rich spherical mesh
    let displayList = [...this.items];
    if (displayList.length > 0 && displayList.length < 12) {
      while (displayList.length < 16) {
        displayList = displayList.concat(this.items);
      }
      displayList = displayList.slice(0, 16);
    }

    const n = displayList.length;
    if (n === 0) return;

    displayList.forEach((item, i) => {
      const card = document.createElement('div');
      card.className = 'sphere-card';

      const inner = document.createElement('div');
      inner.className = 'sphere-card-inner';

      if (item.image && item.image.trim() !== '') {
        const img = document.createElement('img');
        img.src = item.image;
        img.alt = item.name || 'Jopesh Wearable Art';
        img.loading = 'lazy';
        inner.appendChild(img);
      } else {
        const placeholder = document.createElement('div');
        placeholder.className = 'art-placeholder';
        placeholder.innerHTML = `<span style="font-size: 0.65rem; color: #a1a1aa; letter-spacing: 0.1em; text-transform: uppercase;">1 of 1</span><div style="font-size:0.75rem; color:#fff; font-weight:600; margin-top:4px;">${item.name || 'Wearable Art'}</div>`;
        inner.appendChild(placeholder);
      }

      const info = document.createElement('div');
      info.className = 'sphere-card-info';
      info.innerText = item.name || 'Jopesh Piece';
      inner.appendChild(info);

      card.appendChild(inner);

      // Fibonacci Sphere 3D Distribution (Flexible for any number of items)
      const phi = Math.acos(1 - (2 * (i + 0.5)) / n);
      const theta = Math.PI * (1 + Math.sqrt(5)) * i;

      const x = this.radius * Math.cos(theta) * Math.sin(phi);
      const y = this.radius * Math.sin(theta) * Math.sin(phi);
      const z = this.radius * Math.cos(phi);

      const rotY = Math.atan2(x, z) * (180 / Math.PI);
      const rotX = Math.asin(-y / this.radius) * (180 / Math.PI);

      card.style.transform = `translate3d(${x}px, ${y}px, ${z}px) rotateY(${rotY}deg) rotateX(${rotX}deg)`;
      card.dataset.index = i;

      card.addEventListener('click', (e) => {
        e.stopPropagation();
        if (typeof showProductDetailModal === 'function') {
          showProductDetailModal(item);
        }
      });

      this.stage.appendChild(card);
      this.cards.push(card);
    });
  }

  bindEvents() {
    window.addEventListener('resize', () => {
      this.radius = window.innerWidth < 768 ? 190 : 340;
      this.buildCards();
    });

    // Mouse Drag
    this.container.addEventListener('mousedown', (e) => {
      this.isDragging = true;
      this.startX = e.clientX;
      this.startY = e.clientY;
    });

    window.addEventListener('mousemove', (e) => {
      if (!this.isDragging) return;
      const deltaX = e.clientX - this.startX;
      const deltaY = e.clientY - this.startY;
      this.rotY += deltaX * 0.4;
      this.rotX -= deltaY * 0.4;
      this.startX = e.clientX;
      this.startY = e.clientY;
    });

    window.addEventListener('mouseup', () => {
      this.isDragging = false;
    });

    // Touch Drag (Mobile)
    this.container.addEventListener('touchstart', (e) => {
      if (e.touches.length === 1) {
        this.isDragging = true;
        this.startX = e.touches[0].clientX;
        this.startY = e.touches[0].clientY;
      }
    }, { passive: true });

    window.addEventListener('touchmove', (e) => {
      if (!this.isDragging || e.touches.length !== 1) return;
      const deltaX = e.touches[0].clientX - this.startX;
      const deltaY = e.touches[0].clientY - this.startY;
      this.rotY += deltaX * 0.5;
      this.rotX -= deltaY * 0.5;
      this.startX = e.touches[0].clientX;
      this.startY = e.touches[0].clientY;
    }, { passive: true });

    window.addEventListener('touchend', () => {
      this.isDragging = false;
    });
  }

  animate() {
    requestAnimationFrame(() => this.animate());

    if (!this.isDragging) {
      this.rotY += this.autoRotateSpeed;
    }

    // Limit X tilt to prevent flipping inverted
    this.rotX = Math.max(-65, Math.min(65, this.rotX));
    this.stage.style.transform = `rotateX(${this.rotX}deg) rotateY(${this.rotY}deg)`;
  }
}

window.JopeshSphere = JopeshSphere;
