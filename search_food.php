<?php
header('Content-Type: application/json');

$query = strtolower(trim($_GET['q'] ?? ''));

$foods_path = __DIR__ . '/db/foods.json';

if (!file_exists($foods_path)) {
    echo json_encode([]);
    exit;
}

$foods = json_decode(file_get_contents($foods_path), true);
$results = [];

if (!empty($query)) {
    foreach ($foods as $item) {
        if (strpos(strtolower($item['name']), $query) !== false) {
            $results[] = $item;
        }
    }
}

echo json_encode($results);
exit;
?>
