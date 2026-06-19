<?php
/**
 * Страница вывода всех записей дневника настроения в виде HTML-таблицы.
 * Поддерживает сортировку по столбцам через GET-параметр "sort".
 */

/** Путь к файлу с данными (тот же, что используется в process.php). */
const DATA_FILE = __DIR__ . '/data/entries.json';

/** Поля, по которым разрешена сортировка, и их подписи для заголовков таблицы. */
const SORTABLE_FIELDS = [
    'entry_date' => 'Дата',
    'title' => 'Заголовок',
    'mood' => 'Настроение',
    'energy_level' => 'Энергия',
    'created_at' => 'Создано',
];

/**
 * Экранирует строку для безопасного вывода в HTML (защита от XSS).
 *
 * @param string $value Исходное значение.
 * @return string Экранированное значение.
 */
function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Загружает все записи из JSON-файла.
 *
 * @return array Список записей, либо пустой массив, если файла нет.
 */
function loadEntries(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $content = file_get_contents(DATA_FILE);
    $entries = json_decode($content, true);
    return is_array($entries) ? $entries : [];
}

/**
 * Сортирует записи по заданному полю.
 *
 * @param array $entries Список записей.
 * @param string $field Поле для сортировки.
 * @param string $direction Направление: 'asc' или 'desc'.
 * @return array Отсортированный список записей.
 */
function sortEntries(array $entries, string $field, string $direction): array
{
    usort($entries, function ($a, $b) use ($field, $direction) {
        $valA = $a[$field] ?? '';
        $valB = $b[$field] ?? '';

        // Числовое поле сравниваем как числа, остальные — как строки.
        if ($field === 'energy_level') {
            $result = $valA <=> $valB;
        } else {
            $result = strcmp((string)$valA, (string)$valB);
        }

        return $direction === 'desc' ? -$result : $result;
    });

    return $entries;
}

// Определяем поле и направление сортировки из GET-параметров.
$sortField = $_GET['sort'] ?? 'entry_date';
if (!array_key_exists($sortField, SORTABLE_FIELDS)) {
    $sortField = 'entry_date';
}

$sortDirection = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$oppositeDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

$entries = loadEntries();
$entries = sortEntries($entries, $sortField, $sortDirection);

/**
 * Строит URL для заголовка столбца, позволяющий переключать сортировку.
 *
 * @param string $field Имя поля.
 * @return string URL с нужными GET-параметрами.
 */
function sortLinkFor(string $field): string
{
    global $sortField, $sortDirection, $oppositeDirection;
    $direction = $sortField === $field ? $oppositeDirection : 'asc';
    return 'view.php?sort=' . urlencode($field) . '&dir=' . urlencode($direction);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дневник настроения — Все записи</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>📔 Дневник настроения</h1>
    <nav>
        <a href="index.php">Новая запись</a> |
        <a href="view.php">Все записи</a>
    </nav>

    <?php if (empty($entries)): ?>
        <p class="empty-table">Записей пока нет. <a href="index.php">Добавьте первую запись!</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (SORTABLE_FIELDS as $field => $label): ?>
                        <th>
                            <a href="<?= escape(sortLinkFor($field)) ?>">
                                <?= escape($label) ?>
                                <?php if ($sortField === $field): ?>
                                    <?= $sortDirection === 'asc' ? '▲' : '▼' ?>
                                <?php endif; ?>
                            </a>
                        </th>
                    <?php endforeach; ?>
                    <th>Теги</th>
                    <th>Описание</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?= escape($entry['entry_date'] ?? '') ?></td>
                        <td><?= escape($entry['title'] ?? '') ?></td>
                        <td>
                            <?php $moodClass = 'mood-' . mb_strtolower($entry['mood'] ?? ''); ?>
                            <span class="mood-badge <?= escape($moodClass) ?>">
                                <?= escape($entry['mood'] ?? '') ?>
                            </span>
                        </td>
                        <td><?= escape((string)($entry['energy_level'] ?? '')) ?></td>
                        <td><?= escape($entry['created_at'] ?? '') ?></td>
                        <td><?= escape($entry['tags'] ?? '') ?></td>
                        <td><?= nl2br(escape($entry['description'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
