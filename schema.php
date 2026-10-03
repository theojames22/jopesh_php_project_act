<?php
require_once __DIR__ . '/db.php';

// Disable foreign key checks for table recreation if needed
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0;");

// 1. Users Table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $sql_users) or die("Error creating users table: " . mysqli_error($conn));

// 2. Collections Table
$sql_collections = "CREATE TABLE IF NOT EXISTS collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    subtitle VARCHAR(255) NULL,
    description TEXT NULL,
    poster_image VARCHAR(255) NULL,
    release_date VARCHAR(50) DEFAULT '2024',
    is_latest TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $sql_collections) or die("Error creating collections table: " . mysqli_error($conn));

// 3. Products Table
$sql_products = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collection_id INT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image_url VARCHAR(255) NULL,
    status ENUM('available', 'sold', 'auction') DEFAULT 'available',
    size VARCHAR(50) DEFAULT '1 of 1 (Custom Fit)',
    is_sphere_highlight TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $sql_products) or die("Error creating products table: " . mysqli_error($conn));

// 4. Auctions Table
$sql_auctions = "CREATE TABLE IF NOT EXISTS auctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    starting_bid DECIMAL(10,2) NOT NULL,
    current_bid DECIMAL(10,2) NOT NULL,
    bid_increment DECIMAL(10,2) DEFAULT 100.00,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('active', 'ended') DEFAULT 'active',
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $sql_auctions) or die("Error creating auctions table: " . mysqli_error($conn));

// 5. Bids Table
$sql_bids = "CREATE TABLE IF NOT EXISTS bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    user_id INT NOT NULL,
    bid_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $sql_bids) or die("Error creating bids table: " . mysqli_error($conn));

// 6. Orders Table
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    phone VARCHAR(50) NOT NULL,
    order_notes TEXT NULL,
    payment_method VARCHAR(50) DEFAULT 'Bank Transfer / GCash',
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $sql_orders) or die("Error creating orders table: " . mysqli_error($conn));

// 7. Order Items Table
$sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $sql_order_items) or die("Error creating order_items table: " . mysqli_error($conn));

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1;");

// Check if default data exists, if not seed
$check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM collections");
$row = mysqli_fetch_assoc($check);

if ($row['cnt'] == 0) {
    // Seed Demo User
    $demoPassword = password_hash('password123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (username, email, password, full_name, role) VALUES 
        ('jopesh_fan', 'collector@jopesh.com', '$demoPassword', 'Collector Zero', 'customer'),
        ('admin', 'admin@jopesh.com', '$demoPassword', 'Jopesh Admin', 'admin')");

    // Seed Collections
    mysqli_query($conn, "INSERT INTO collections (title, code, slug, subtitle, description, poster_image, release_date, is_latest, sort_order) VALUES
        ('Collection IV: Decay & Rebirth', 'COLLECTION IV', 'collection-4', '1 of 1 Wearable Art', 'The fourth installment exploring tactile decay, oversized silhouettes, and heavy distressing.', '', '2024', 1, 1),
        ('Collection III: Flesh & Shadow', 'COLLECTION III', 'collection-3', 'Underground Tailoring', 'Deconstructed formalwear infused with raw gothic poetry and hand-stitched details.', '', '2023', 0, 2),
        ('Collection II: Industrial Soul', 'COLLECTION II', 'collection-2', 'Raw Materials & Hardware', 'Heavy metal hardware, silver safety pins, and distressed dark denim armor.', '', '2023', 0, 3),
        ('Collection I: Genesis', 'COLLECTION I', 'collection-1', 'Where It All Began', 'The foundational 1 of 1 wearable art pieces that forged the Jopesh identity.', '', '2022', 0, 4)
    ");

    $col4_id = 1;
    $col3_id = 2;
    $col2_id = 3;
    $col1_id = 4;

    // Seed Products:
    // User requested empty images first since they have their own images to put in.
    // 1. Auctions
    mysqli_query($conn, "INSERT INTO products (collection_id, name, description, price, image_url, status, size, is_sphere_highlight) VALUES
        ($col4_id, 'Bloodline Puppet Longcoat', 'Hand-tailored 1-of-1 sculptural piece with contrasting red thread, raw edge hems, and bespoke hardware.', 4500.00, '', 'auction', '1 of 1 (Custom Fit)', 1),
        ($col4_id, 'Abyssal Deconstructed Kimono Vest', 'Oversized heavy canvas with layered distressing, industrial rings, and distressed script.', 3800.00, '', 'auction', '1 of 1 (Custom Fit)', 1)
    ");

    $prod_auc1 = 1;
    $prod_auc2 = 2;

    $now = date('Y-m-d H:i:s');
    $end1 = date('Y-m-d H:i:s', strtotime('+2 days 5 hours'));
    $end2 = date('Y-m-d H:i:s', strtotime('+1 day 14 hours'));

    mysqli_query($conn, "INSERT INTO auctions (product_id, starting_bid, current_bid, bid_increment, start_time, end_time, status) VALUES
        ($prod_auc1, 3000.00, 3500.00, 100.00, '$now', '$end1', 'active'),
        ($prod_auc2, 3500.00, 4000.00, 100.00, '$now', '$end2', 'active')
    ");

    // Add bids for auction 1 (sketch showed highest bid 3500, previous bids 3200, 3000)
    mysqli_query($conn, "INSERT INTO bids (auction_id, user_id, bid_amount, created_at) VALUES
        (1, 1, 3000.00, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
        (1, 1, 3200.00, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
        (1, 1, 3500.00, DATE_SUB(NOW(), INTERVAL 30 MINUTE))
    ");

    // Add bids for auction 2 (sketch showed highest bid 4000, previous bids 3700, 3600)
    mysqli_query($conn, "INSERT INTO bids (auction_id, user_id, bid_amount, created_at) VALUES
        (2, 1, 3600.00, DATE_SUB(NOW(), INTERVAL 5 HOUR)),
        (2, 1, 3700.00, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
        (2, 1, 4000.00, DATE_SUB(NOW(), INTERVAL 45 MINUTE))
    ");

    // 2. Available Pieces (Section 5 - Layout by fours)
    mysqli_query($conn, "INSERT INTO products (collection_id, name, description, price, image_url, status, size, is_sphere_highlight) VALUES
        ($col4_id, 'Crimson Cross Distressed Flares', 'Heavyweight washed black denim with hand-cut gothic cross patches and frayed hems.', 3200.00, '', 'available', '30-32 Waist', 1),
        ($col4_id, 'Decay Reconstructed Raw Jacket', 'Multi-panel upcycled denim jacket with safety pin accents and exposed seamlines.', 4200.00, '', 'available', 'Boxy Large', 1),
        ($col4_id, 'Gothic Shadow Calligraphy Hoodie', 'Oversized fleece hoodie featuring hand-painted Japanese gothic motifs.', 2800.00, '', 'available', 'Oversized Free Size', 1),
        ($col4_id, 'Industrial D-Ring Cargo Trousers', 'Tactical pocket silhouette with heavy metal clip fasteners and raw hems.', 3100.00, '', 'available', '32-34 Waist', 1),
        ($col4_id, 'Venomous Stitched Heavy Overshirt', 'Waxed cotton overshirt with asymmetrical distressed pocket and silver studs.', 3400.00, '', 'available', 'Large', 1),
        ($col4_id, 'Phantom Monolith Sleeveless Tunic', 'Heavy draped silhouette with deep side slits and raw edge distressing.', 2200.00, '', 'available', 'One Size', 1)
    ");

    // 3. Sold Pieces (Section 7 - Layout by fours)
    mysqli_query($conn, "INSERT INTO products (collection_id, name, description, price, image_url, status, size, is_sphere_highlight) VALUES
        ($col3_id, 'Crucifix Patchwork Trench Coat', 'Archival 1 of 1 masterwork piece made for runway presentation.', 5500.00, '', 'sold', '1 of 1', 0),
        ($col3_id, 'Serrated Edge Velvet Blazer', 'Distressed black velvet tailoring with raw crimson silk lining.', 4800.00, '', 'sold', '1 of 1', 0),
        ($col3_id, 'Obsidian Hand-Dyed Oversized Tee', 'Washed acid treated heavyweight cotton with distressing.', 1800.00, '', 'sold', '1 of 1', 0),
        ($col3_id, 'Chained Cargo Utility Pants', 'Archival multi-chain combat trousers with heavy wear.', 3600.00, '', 'sold', '1 of 1', 0),
        ($col2_id, 'Fragmented Leather Rider Jacket', 'Hand-assembled reconstructed vintage leather biker jacket.', 6500.00, '', 'sold', '1 of 1', 0),
        ($col2_id, 'Rotted Seam Raw Knit Sweater', 'Distressed open-weave distressed mohair knitwear.', 3200.00, '', 'sold', '1 of 1', 0),
        ($col1_id, 'Genesis Prototype Zero Vest', 'First historical Jopesh test piece, distressed canvas and metal.', 4000.00, '', 'sold', '1 of 1', 0),
        ($col1_id, 'Occult Script Asymmetric Shirt', 'Draped collar formal shirt with hand-inked occult calligraphy.', 2900.00, '', 'sold', '1 of 1', 0)
    ");

    // Additional sphere highlight pieces for the "Malupit na bola" (latest collection pieces)
    mysqli_query($conn, "INSERT INTO products (collection_id, name, description, price, image_url, status, size, is_sphere_highlight) VALUES
        ($col4_id, 'Sculptural Blood Spatter Trousers', 'Custom paint splattered raw canvas trousers.', 3100.00, '', 'available', '1 of 1', 1),
        ($col4_id, 'Silver Ring Hand-Distressed Beanie', 'Heavy ribbed knit with inserted chrome rings.', 1200.00, '', 'available', 'Free Size', 1),
        ($col4_id, 'Bleached Gothic Graphic Longsleeve', 'Distressed thermal longsleeve with custom acid discharge.', 2400.00, '', 'available', 'Oversized', 1),
        ($col4_id, 'Bone Relic Hardware Belt', 'Hand-forged buckle on thick distressed leather belt.', 1500.00, '', 'available', 'Adjustable', 1)
    ");
}

echo "Database initialized successfully.\n";
?>
