<?php
header('Content-Type: application/json');

$replicate_api_token = "R8_U7Y2QNtLKwKV8H7ynE3DYX9yeTwjDv716hX9a";

$api_endpoint = "https://api.replicate.com/v1/models/black-forest-labs/flux-1.1-pro/predictions";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['user_photo']) || $_FILES['user_photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Please upload a valid image']);
    exit;
}

$style_preference = filter_input(INPUT_POST, 'style_preference', FILTER_SANITIZE_SPECIAL_CHARS);
if (empty($style_preference)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a hairstyle or color preference']);
    exit;
}

$tmp_name = $_FILES['user_photo']['tmp_name'];
$type = $_FILES['user_photo']['type'];
$data = file_get_contents($tmp_name);
$base64_image = 'data:' . $type . ';base64,' . base64_encode($data);

$prompt = "A high quality, professional salon photo of this person with " . $style_preference . " hairstyle, photorealistic, 8k resolution, realistic lighting";

$payload = [
    'input' => [
        'prompt' => $prompt,
        'image' => $base64_image,
        'aspect_ratio' => '1:1',
        'output_format' => 'jpg',
        'output_quality' => 90
    ]
];

$ch = curl_init($api_endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $replicate_api_token,
    'Content-Type: application/json',
    'Prefer: wait'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($http_code !== 201 && $http_code !== 200) {
    $error_msg = $result['detail'] ?? ($result['error'] ?? 'Failed to initialize API request');
    echo json_encode(['success' => false, 'message' => $error_msg]);
    exit;
}

if (isset($result['status']) && $result['status'] === 'succeeded') {
    $output_url = is_array($result['output']) ? $result['output'][0] : $result['output'];
    echo json_encode(['success' => true, 'image_url' => $output_url]);
    exit;
}

if (isset($result['urls']['get'])) {
    $prediction_url = $result['urls']['get'];
    $output_image_url = null;
    $attempts = 0;
    $max_attempts = 30;

    while ($attempts < $max_attempts) {
        sleep(1);
        
        $ch = curl_init($prediction_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $replicate_api_token,
            'Content-Type: application/json'
        ]);
        
        $poll_response = curl_exec($ch);
        curl_close($ch);
        
        $poll_result = json_decode($poll_response, true);
        
        if ($poll_result['status'] === 'succeeded') {
            $output_image_url = is_array($poll_result['output']) ? $poll_result['output'][0] : $poll_result['output'];
            break;
        } elseif ($poll_result['status'] === 'failed' || $poll_result['status'] === 'canceled') {
            echo json_encode(['success' => false, 'message' => 'AI generation failed: ' . ($poll_result['error'] ?? 'Unknown error')]);
            exit;
        }
        
        $attempts++;
    }

    if ($output_image_url) {
        echo json_encode(['success' => true, 'image_url' => $output_image_url]);
    } else {
        echo json_encode(['success' => false, 'message' => 'AI processing timed out. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Unexpected API response format']);
}