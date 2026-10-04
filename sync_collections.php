<?php
header('Content-Type: text/plain; charset=utf-8');

$root = __DIR__ . '/assets/images/jopesh collections';

function parseUnicodeName($str) {
    // Convert math bold / unicode styled letters into normal text if needed
    // 𝐀-𝐙 is U+1D400 to U+1D419 (A-Z)
    // 𝐚-𝐳 is U+1D41A to U+1D433 (a-z)
    // 𝗔-𝗭 is U+1D5D4 to U+1D5ED
    // 𝗮-𝘇 is U+1D5EE to U+1D607
    $normal = '';
    $chars = mb_str_split($str);
    foreach ($chars as $ch) {
        $cp = mb_ord($ch);
        if ($cp >= 0x1D400 && $cp <= 0x1D419) {
            $normal .= chr(65 + ($cp - 0x1D400));
        } elseif ($cp >= 0x1D41A && $cp <= 0x1D433) {
            $normal .= chr(97 + ($cp - 0x1D41A));
        } elseif ($cp >= 0x1D5D4 && $cp <= 0x1D5ED) {
            $normal .= chr(65 + ($cp - 0x1D5D4));
        } elseif ($cp >= 0x1D5EE && $cp <= 0x1D607) {
            $normal .= chr(97 + ($cp - 0x1D5EE));
        } else {
            $normal .= $ch;
        }
    }
    return $normal;
}

$collections = [];

// 1. Nocturne Collection
$nocturneDir = $root . '/nocturne collection';
if (is_dir($nocturneDir)) {
    $col = [
        'name' => 'Nocturne Collection',
        'code' => 'NOCTURNE',
        'slug' => 'nocturne-collection',
        'cover' => 'assets/images/jopesh collections/nocturne collection/cover photo.jpg',
        'desc' => file_exists($nocturneDir . '/nocturne description.txt') ? file_get_contents($nocturneDir . '/nocturne description.txt') : '',
        'pieces' => []
    ];

    $items = scandir($nocturneDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $nocturneDir . '/' . $item;
        if (is_dir($path)) {
            $cleanName = parseUnicodeName($item);
            $descText = file_exists($path . '/description.txt') ? file_get_contents($path . '/description.txt') : '';
            
            // Find images
            $images = [];
            $imgFiles = scandir($path);
            foreach ($imgFiles as $f) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)) {
                    $images[] = 'assets/images/jopesh collections/nocturne collection/' . $item . '/' . $f;
                }
            }
            
            // Primary image (front, sample, or first image)
            $primary = '';
            foreach ($images as $img) {
                if (stripos($img, 'front') !== false) {
                    $primary = $img;
                    break;
                }
            }
            if (!$primary && count($images) > 0) {
                $primary = $images[0];
            }

            $col['pieces'][] = [
                'raw_name' => $item,
                'name' => $cleanName,
                'primary_image' => $primary,
                'images' => $images,
                'description' => $descText
            ];
        }
    }
    $collections['nocturne'] = $col;
}

// 2. Archive Collection
$archDir = $root . '/archive collection';
if (is_dir($archDir)) {
    $col = [
        'name' => 'Archive Collection',
        'code' => 'ARCHIVE',
        'slug' => 'archive-collection',
        'cover' => '',
        'desc' => 'Archival one-of-one reworked masterworks and foundational Jopesh pieces.',
        'pieces' => []
    ];

    $items = scandir($archDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $archDir . '/' . $item;
        if (is_dir($path)) {
            $cleanName = parseUnicodeName($item);
            $descText = file_exists($path . '/description.txt') ? file_get_contents($path . '/description.txt') : '';
            
            $images = [];
            $imgFiles = scandir($path);
            foreach ($imgFiles as $f) {
                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)) {
                    $images[] = 'assets/images/jopesh collections/archive collection/' . $item . '/' . $f;
                }
            }
            $primary = '';
            foreach ($images as $img) {
                if (stripos($img, 'front') !== false) {
                    $primary = $img;
                    break;
                }
            }
            if (!$primary && count($images) > 0) $primary = $images[0];

            $col['pieces'][] = [
                'raw_name' => $item,
                'name' => $cleanName,
                'primary_image' => $primary,
                'images' => $images,
                'description' => $descText
            ];
        }
    }
    if (count($col['pieces']) > 0 && empty($col['cover'])) {
        $col['cover'] = $col['pieces'][0]['primary_image'];
    }
    $collections['archive'] = $col;
}

// 3. Relic Crimson
$relicDir = $root . '/𝐑𝐞𝐥𝐢𝐜 𝐂𝐫𝐢𝐦𝐬𝐨𝐧';
if (is_dir($relicDir)) {
    $descText = file_exists($relicDir . '/description.txt') ? file_get_contents($relicDir . '/description.txt') : '';
    $images = [];
    $imgFiles = scandir($relicDir);
    foreach ($imgFiles as $f) {
        if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)) {
            $images[] = 'assets/images/jopesh collections/𝐑𝐞𝐥𝐢𝐜 𝐂𝐫𝐢𝐦𝐬𝐨𝐧/' . $f;
        }
    }
    $primary = '';
    foreach ($images as $img) {
        if (stripos($img, 'front') !== false) {
            $primary = $img;
            break;
        }
    }
    if (!$primary && count($images) > 0) $primary = $images[0];

    $col = [
        'name' => 'Relic Crimson',
        'code' => 'RELIC CRIMSON',
        'slug' => 'relic-crimson',
        'cover' => $primary,
        'desc' => $descText,
        'pieces' => [[
            'raw_name' => '𝐑𝐞𝐥𝐢𝐜 𝐂𝐫𝐢𝐦𝐬𝐨𝐧',
            'name' => 'Relic Crimson',
            'primary_image' => $primary,
            'images' => $images,
            'description' => $descText
        ]]
    ];
    $collections['relic'] = $col;
}

file_put_contents(__DIR__ . '/collections_data.json', json_encode($collections, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Successfully extracted collections and saved to collections_data.json\n";
foreach ($collections as $key => $col) {
    echo "- " . $col['name'] . " (" . count($col['pieces']) . " pieces)\n";
    foreach ($col['pieces'] as $p) {
        echo "   * " . $p['name'] . " (" . count($p['images']) . " images)\n";
    }
}
?>
