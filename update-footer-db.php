<?php
require_once 'config.php';

$conn = getConnection();

// Update database structure
$queries = [
    "ALTER TABLE footer DROP COLUMN address",
    "ALTER TABLE footer DROP COLUMN phone", 
    "ALTER TABLE footer DROP COLUMN email",
    "ALTER TABLE footer ADD COLUMN description TEXT AFTER id",
    "ALTER TABLE footer ADD COLUMN copyright VARCHAR(255) AFTER description"
];

echo "Updating footer table structure...\n<br>\n";

foreach ($queries as $query) {
    echo "Executing: $query\n<br>\n";
    if ($conn->query($query)) {
        echo "✓ Success\n<br>\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n<br>\n";
    }
}

// Update existing footer data with default values
$updateData = $conn->query("UPDATE footer SET 
    description = '@nt''s Arena adalah lapangan bulutangkis terbaik di kota dengan fasilitas lengkap dan modern. Kami berkomitmen memberikan pengalaman bermain terbaik untuk Anda.',
    copyright = '© 2025 @nt''s Arena - Semua Hak Dilindungi'
    WHERE description IS NULL OR description = ''");

if ($updateData) {
    echo "\n<br>✓ Default data inserted successfully!\n<br>";
} else {
    echo "\n<br>✗ Error updating data: " . $conn->error . "\n<br>";
}

echo "\n<br><strong>Database migration completed!</strong>\n<br>";
echo "<a href='admin-footer.php'>Go to Footer Admin Panel</a>";

$conn->close();
?>
