<?php
$apiKey = 'OPENAI_API_KEY'; // Replace with your actual API key

$query = isset($_GET['query']) ? $_GET['query'] : '';

if (empty($query)) {
    echo "No query provided.";
    exit;
}

$ch = curl_init('https://api.openai.com/v1/chat/completions');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json',
]);

$prompt = "Generate 3 recipes for '{$query}' in plain text format. URL must be of valid website. Each recipe must include the following:
    - Title: [Recipe Title]
    - URL: [URL from a valid website, if available, or 'Not available']
    - Image URL: [URL of the recipe from the website, if available, or 'Not available']
    - Ingredients:
    - Directions:
   
    Separate each recipe with a line of dashes ('-----'). Ensure URLs are taken from valid websites.";
    
    

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful assistant that generates recipes in plain text.'],
        ['role' => 'user', 'content' => $prompt],
    ],
    'max_tokens' => 1000,
    'temperature' => 0.7,
]));

$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo "Error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

if ($httpStatus !== 200) {
    echo "Error: HTTP Status " . $httpStatus;
    curl_close($ch);
    exit;
}

$responseData = json_decode($response, true);

// Extract the content field for simpler front-end processing
if (isset($responseData['choices'][0]['message']['content'])) {
    echo $responseData['choices'][0]['message']['content'];
} else {
    echo "No content returned from API.";
}

curl_close($ch);
?>





