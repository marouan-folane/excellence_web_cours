<?php

// This script copies the logo image to the correct location

// Define the source and target paths
$sourcePath = __DIR__ . '/../@excellence.png'; // Check if logo is in the project root
$alternativeSource = __DIR__ . '/@excellence.png'; // Check if logo is in the public folder
$targetPath = __DIR__ . '/images/@excellence.png';

echo "<h1>Logo File Copy Utility</h1>";

// Check if the target directory exists
if (!is_dir(__DIR__ . '/images')) {
    if (mkdir(__DIR__ . '/images', 0755, true)) {
        echo "<p>Created directory: " . __DIR__ . '/images' . "</p>";
    } else {
        echo "<p style='color: red;'>Failed to create directory: " . __DIR__ . '/images' . "</p>";
    }
}

// Check if the source file exists
if (file_exists($sourcePath)) {
    echo "<p>Found logo at: $sourcePath</p>";
    $source = $sourcePath;
} elseif (file_exists($alternativeSource)) {
    echo "<p>Found logo at: $alternativeSource</p>";
    $source = $alternativeSource;
} else {
    echo "<p style='color: red;'>Logo file not found in expected locations:</p>";
    echo "<ul>";
    echo "<li>" . $sourcePath . "</li>";
    echo "<li>" . $alternativeSource . "</li>";
    echo "</ul>";
    echo "<p>Please upload your @excellence.png logo file to one of these locations.</p>";
    exit;
}

// Copy the file
if (copy($source, $targetPath)) {
    echo "<p style='color: green;'>Logo successfully copied to: $targetPath</p>";
    echo "<p>You can now generate a receipt with the logo.</p>";
    
    // Additional verification
    if (file_exists($targetPath)) {
        echo "<p>Verified file exists at target location!</p>";
        echo "<img src='/images/@excellence.png' alt='Logo Preview' style='max-width: 200px; border: 1px solid #ccc; padding: 10px;'>";
    } else {
        echo "<p style='color: red;'>File copy reported success but verification failed!</p>";
    }
} else {
    echo "<p style='color: red;'>Failed to copy logo file!</p>";
    echo "<p>Check file permissions or manually copy your logo file to: " . $targetPath . "</p>";
}

// List files in the images directory
echo "<h2>Files in the images directory:</h2>";
echo "<ul>";
if ($handle = opendir(__DIR__ . '/images')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo "<li>$entry</li>";
        }
    }
    closedir($handle);
} else {
    echo "<li>Could not open the images directory</li>";
}
echo "</ul>";

// PHP Info for debugging
echo "<h2>PHP Information:</h2>";
echo "<p>GD Extension loaded: " . (extension_loaded('gd') ? 'Yes' : 'No') . "</p>";
echo "<p>Current working directory: " . getcwd() . "</p>";
echo "<p>Server software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>"; 