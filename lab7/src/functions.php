<?php

define('DATA_FILE', __DIR__ . '/../data/recipes.json');

function getRecipes(): array {
    if (!file_exists(DATA_FILE)) return [];
    $json = file_get_contents(DATA_FILE);
    return json_decode($json, true) ?? [];
}

function saveRecipes(array $recipes): void {
    file_put_contents(DATA_FILE, json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function addRecipe(string $name, string $category, int $prepTime, int $servings, string $description): void {
    $recipes = getRecipes();
    $maxId = array_reduce($recipes, fn($carry, $r) => max($carry, $r['id']), 0);
    $recipes[] = [
        'id'          => $maxId + 1,
        'name'        => $name,
        'category'    => $category,
        'prep_time'   => $prepTime,
        'servings'    => $servings,
        'description' => $description,
    ];
    saveRecipes($recipes);
}

function deleteRecipe(int $id): void {
    $recipes = getRecipes();
    $recipes = array_values(array_filter($recipes, fn($r) => $r['id'] !== $id));
    saveRecipes($recipes);
}

function formatDuration(int $minutes): string {
    if ($minutes < 60) return "{$minutes} мин";
    $h = intdiv($minutes, 60);
    $m = $minutes % 60;
    return $m > 0 ? "{$h} ч {$m} мин" : "{$h} ч";
}
