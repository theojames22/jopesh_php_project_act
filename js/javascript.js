// Register GSAP Plugins
gsap.registerPlugin(ScrollTrigger);

// Provided Image Assets
const images = [
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/xtra-group6.jpg?v=1764351424",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/dp-pack2.jpg?v=1764343183",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/cc-dp.jpg?v=1764280792",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/cc-serum.jpg?v=1764279474",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/cc-col-square.webp?v=1762416360",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/dp-bag-square-01-01.webp?v=1757416671",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/dermd-products.png?v=1753033534",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/candle-square-05-01.webp?v=1750833640",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/cps-square-01-01.webp?v=1748419375",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/exf-square-02.jpg?v=1750856965",
  "https://cdn.shopify.com/s/files/1/0185/5999/1872/files/fes-square-02.webp?v=1748941779"
];

// Duplicate to create a full dense sphere
const galleryItems = [...images, ...images, ...images].slice(0, 24);

const textContent = [
  {
    title: "Running Through Reflection",
    desc:
      "One photo was taken on a rainy day at a running track. It is a tape of water covered the surface..."
  },
  {
    title: "Elegance in Boldness",
    desc:
      "This photo was captured in a controlled studio environment, emphasizing strong features and deep contrast."
  },
  {
    title: "Dynamic Moments",
    desc:
      "Capturing the pure essence of speed and raw emotion in a split-second frame."
  },
  {
    title: "Abstract Movement",
    desc:
      "The flow of time expressed visually through long exposures and deliberate camera movement."
  }
];

const sphere = document.getElementById("sphere");
const radius = window.innerWidth < 768 ? 200 : 380;

// 1. Generate Fibonacci Sphere 3D Layout
galleryItems.forEach((src, i) => {
  const card = document.createElement("div");
  card.classList.add("clay-card");

  const img = document.createElement("img");
  img.src = src;
  card.appendChild(img);

  // Math for Fibonacci sphere distribution
  const phi = Math.acos(1 - (2 * (i + 0.5)) / galleryItems.length);
  const theta = Math.PI * (1 + Math.sqrt(5)) * i;

  const x = radius * Math.cos(theta) * Math.sin(phi);
  const y = radius * Math.sin(theta) * Math.sin(phi);
  const z = radius * Math.cos(phi);

  // Calculate rotation so cards face outward relative to the center
  const rotY = Math.atan2(x, z) * (180 / Math.PI);
  const rotX = Math.asin(-y / radius) * (180 / Math.PI);

  card.style.transform = `translate3d(${x}px, ${y}px, ${z}px) rotateY(${rotY}deg) rotateX(${rotX}deg)`;

  // Store original Z rotation for scroll highlighting
  card.dataset.index = i;
  sphere.appendChild(card);
});

// 2. Animate Sphere on Scroll
let sphereRotation = gsap.to(sphere, {
  rotateY: 360 * 2, // 2 full rotations
  rotateX: 45, // Slight tilt for 3D depth
  ease: "none",
  scrollTrigger: {
    trigger: ".gallery-container",
    start: "top top",
    end: "bottom bottom",
    scrub: 1,
    onUpdate: (self) => updateActiveCard(self.progress)
  }
});

// 3. Highlight Front-Facing Card and Update Text
const titleEl = document.getElementById("dynamic-title");
const descEl = document.getElementById("dynamic-desc");
const allCards = document.querySelectorAll(".clay-card");

function updateActiveCard(progress) {
  // Determine which text index to show based on progress
  const textIndex =
    Math.floor(progress * textContent.length) % textContent.length;

  if (titleEl.innerText !== textContent[textIndex].title) {
    // Fade out/in effect for text
    gsap.to([titleEl, descEl], {
      opacity: 0,
      duration: 0.2,
      onComplete: () => {
        titleEl.innerText = textContent[textIndex].title;
        descEl.innerText = textContent[textIndex].desc;
        gsap.to([titleEl, descEl], { opacity: 1, duration: 0.2 });
      }
    });
  }

  // Highlight a few cards simulating coming to the front focus
  const focusIndex = Math.floor(progress * galleryItems.length);
  allCards.forEach((card, idx) => {
    if (Math.abs(idx - focusIndex) < 2) {
      card.classList.add("active-card");
    } else {
      card.classList.remove("active-card");
    }
  });
}

// 4. Generate Scatter/Constellation in Journey Section
const constellation = document.getElementById("constellation");
const scatterPositions = [
  { top: "10%", left: "15%" },
  { top: "20%", left: "80%" },
  { top: "60%", left: "10%" },
  { top: "75%", left: "85%" },
  { top: "85%", left: "30%" },
  { top: "15%", left: "50%" }
];

scatterPositions.forEach((pos, i) => {
  const card = document.createElement("div");
  card.classList.add("clay-card", "constellation-card");
  card.style.top = pos.top;
  card.style.left = pos.left;

  // Add varying rotation for messy scatter look
  const rot = (Math.random() - 0.5) * 40;
  card.style.transform = `rotate(${rot}deg) scale(0.8)`;

  const img = document.createElement("img");
  img.src = images[i % images.length];
  card.appendChild(img);

  constellation.appendChild(card);
});

// Parallax effect for scattered cards
gsap.to(".constellation-card", {
  y: -100,
  ease: "none",
  stagger: 0.1,
  scrollTrigger: {
    trigger: ".journey-section",
    start: "top bottom",
    end: "bottom top",
    scrub: true
  }
});
