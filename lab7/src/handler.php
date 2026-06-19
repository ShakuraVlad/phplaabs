<?php

require_once __DIR__ . '/functions.php';

function handleRequest(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name        = trim($_POST['name'] ?? '');
            $category    = trim($_POST['category'] ?? '');
            $prepTime    = (int)($_POST['prep_time'] ?? 0);
            $servings    = (int)($_POST['servings'] ?? 1);
            $description = trim($_POST['description'] ?? '');

            if ($name && $category && $prepTime > 0) {
                addRecipe($name, $category, $prepTime, $servings, $description);
            }
        }

        if ($_POST['action'] === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) deleteRecipe($id);
        }

        // Redirect to avoid form resubmission
        $mode = $_GET['mode'] ?? 'native';
        header("Location: index.php?mode={$mode}");
        exit;
    }
}
