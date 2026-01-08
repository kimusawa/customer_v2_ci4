<?php

// Correct way to boot CI4 for a standalone script
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

$db = \Config\Database::connect();

echo "--- Table Indexes for spgsuser ---\n";
$query = $db->query("SHOW INDEX FROM spgsuser");
foreach ($query->getResultArray() as $row) {
    echo "Table: {$row['Table']}, Non_unique: {$row['Non_unique']}, Key_name: {$row['Key_name']}, Column_name: {$row['Column_name']}\n";
}

echo "\n--- Table Status for spgsuser ---\n";
$query = $db->query("SHOW TABLE STATUS LIKE 'spgsuser'");
$status = $query->getRowArray();
echo "Rows: {$status['Rows']}, Data_length: {$status['Data_length']}, Index_length: {$status['Index_length']}\n";

$start = microtime(true);
$count = $db->table('spgsuser')->countAllResults();
$end = microtime(true);
echo "\nTotal Count (direct countAllResults): " . $count . "\n";
echo "Time taken: " . ($end - $start) . " seconds\n";
