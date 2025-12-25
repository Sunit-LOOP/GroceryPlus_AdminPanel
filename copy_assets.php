<?php
$source = 'D:/6TH PROJECT/GroceryPLus/app/src/main/res/drawable/';
$dest = 'assets/';

if (!is_dir($dest)) {
    mkdir($dest, 0755, true);
}

$files = scandir($source);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        copy($source . $file, $dest . $file);
    }
}

echo "Assets copied successfully.";
?>