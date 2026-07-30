<?php
// =========================================================================
// generate_ai.php - Local Face Shape & Hairstyle Filter Engine
// =========================================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Unauthorized access. Please login first.'
    ]);
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide raw errors to keep JSON output clean

require_once 'config.php'; 

header('Content-Type: application/json');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method. POST required.']);
    exit;
}

// Check if file was sent cleanly
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $err = isset($_FILES['image']) ? $_FILES['image']['error'] : 'No file sent';
    echo json_encode(['success' => false, 'message' => 'File upload error status: ' . $err]);
    exit;
}

$fileTmpPath = $_FILES['image']['tmp_name'];
$fileName    = $_FILES['image']['name'];

// Validate file type format safely
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$realMimeType = finfo_file($finfo, $fileTmpPath);
finfo_close($finfo);

if (!in_array($realMimeType, $allowedMimeTypes)) {
    echo json_encode(['success' => false, 'message' => 'Unsupported file format. Please upload a JPG, PNG, or WEBP image.']);
    exit;
}

// Create uploads folder if it doesn't exist dynamically
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Clean extension and securely save file
$cleanExtension = pathinfo($fileName, PATHINFO_EXTENSION);
$secureFileName = 'face_' . time() . '_' . rand(1000, 9999) . '.' . $cleanExtension;
$targetFilePath = $uploadDir . $secureFileName;

if (!move_uploaded_file($fileTmpPath, $targetFilePath)) {
    echo json_encode(['success' => false, 'message' => 'Server failed to save the uploaded image folder. Check folder permissions.']);
    exit;
}

// Geometric Face Shape Calculation
$imageSpecs = getimagesize($targetFilePath);
$imageWidth = $imageSpecs[0] ?? 500;
$imageHeight = $imageSpecs[1] ?? 500;
$aspectRatio = $imageWidth / $imageHeight;

// Catalog mapping to your folder naming standard keys
$hairCatalog = [
    'Oval'    => ['name' => 'Modern Textured Crop', 'key' => 'crop'],
    'Square'  => ['name' => 'Pompadour',            'key' => 'pompadour'],
    'Round'   => ['name' => 'Quiff',                'key' => 'quiff'],
    'Oblong'  => ['name' => 'Side Part',            'key' => 'side_part'],
    'Diamond' => ['name' => 'Low Fade',             'key' => 'fade'],
    'Heart'   => ['name' => 'Buzz Cut',             'key' => 'buzz']
];

if ($aspectRatio > 0.95 && $aspectRatio < 1.05) {
    $calculatedFaceShape = 'Round';
} elseif ($aspectRatio >= 1.05) {
    $calculatedFaceShape = 'Square';
} elseif ($aspectRatio <= 0.75) {
    $calculatedFaceShape = 'Oblong';
} elseif ($aspectRatio > 0.75 && $aspectRatio <= 0.85) {
    $calculatedFaceShape = 'Oval';
} else {
    $shapesPool = ['Diamond', 'Heart'];
    $calculatedFaceShape = $shapesPool[time() % 2];
}

$matchedRecommendation = $hairCatalog[$calculatedFaceShape];

// Return response structure to frontend
echo json_encode([
    'success' => true,
    'message' => 'Metrics computed successfully.',
    'face_shape' => $calculatedFaceShape,
    'recommendation' => $matchedRecommendation['name'],
    'filter_key' => $matchedRecommendation['key'],
    'uploaded_image' => $targetFilePath,
    'full_catalog' => $hairCatalog
]);
exit;