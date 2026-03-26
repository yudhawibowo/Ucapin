<?php
/**
 * Get Text References API
 * Returns text references filtered by category
 */
require_once '../config/database.php';
header('Content-Type: application/json');

$db = (new Database())->connect();

$categoryId = $_GET['category_id'] ?? 'all';

if ($categoryId === 'all') {
    $stmt = $db->query("SELECT id, content FROM text_references ORDER BY created_at DESC");
} else {
    $stmt = $db->prepare("SELECT id, content FROM text_references WHERE category_id = ? ORDER BY created_at DESC");
    $stmt->execute([$categoryId]);
}

$texts = $stmt->fetchAll();
echo json_encode($texts);
