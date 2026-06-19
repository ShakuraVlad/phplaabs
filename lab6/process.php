<?php
/**
 * Скрипт обработки данных формы "Дневник настроения".
 *
 * Принимает данные из $_POST, выполняет серверную валидацию,
 * и в случае успеха добавляет новую запись в файл data/entries.json.
 * При ошибках валидации возвращает пользователя на форму с описанием проблем
 * и сохранёнными введёнными значениями (чтобы не вводить всё заново).
 */
session_start();

/** Путь к файлу, в котором хранятся все записи в формате JSON. */
const DATA_FILE = __DIR__ . '/data/entries.json';

/** Список допустимых значений настроения (должен совпадать с формой). */
const ALLOWED_MOODS = ['Радостно', 'Спокойно', 'Грустно', 'Тревожно', 'Злюсь'];

/**
 * Проверяет данные, пришедшие из формы, и возвращает список ошибок.
 * Если массив пуст — данные валидны.
 *
 * @param array $data Сырые данные из $_POST.
 * @return array<string> Список текстовых сообщений об ошибках.
 */
function validate(array $data): array
{
    $errors = [];

    // Заголовок: обязателен, длина от 3 до 100 символов.
    $title = trim($data['title'] ?? '');
    if ($title === '') {
        $errors[] = 'Поле "Заголовок записи" обязательно для заполнения.';
    } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 100) {
        $errors[] = 'Заголовок должен содержать от 3 до 100 символов.';
    }

    // Дата: обязательна и должна соответствовать формату YYYY-MM-DD.
    $date = $data['entry_date'] ?? '';
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if ($date === '' || !$dateObj || $dateObj->format('Y-m-d') !== $date) {
        $errors[] = 'Поле "Дата" обязательно и должно быть корректной датой.';
    }

    // Настроение: обязательно и должно входить в список допустимых значений.
    $mood = $data['mood'] ?? '';
    if ($mood === '' || !in_array($mood, ALLOWED_MOODS, true)) {
        $errors[] = 'Выберите одно из предложенных значений настроения.';
    }

    // Уровень энергии: число от 1 до 10.
    $energy = $data['energy_level'] ?? '';
    if ($energy === '' || !is_numeric($energy) || (int)$energy < 1 || (int)$energy > 10) {
        $errors[] = 'Уровень энергии должен быть числом от 1 до 10.';
    }

    // Описание: обязательно, от 10 до 2000 символов.
    $description = trim($data['description'] ?? '');
    if ($description === '') {
        $errors[] = 'Поле "Описание дня" обязательно для заполнения.';
    } elseif (mb_strlen($description) < 10 || mb_strlen($description) > 2000) {
        $errors[] = 'Описание должно содержать от 10 до 2000 символов.';
    }

    // Теги необязательны, но если указаны — ограничим длину.
    $tags = trim($data['tags'] ?? '');
    if (mb_strlen($tags) > 150) {
        $errors[] = 'Поле "Теги" не должно превышать 150 символов.';
    }

    return $errors;
}

/**
 * Считывает текущие записи из JSON-файла.
 * Если файл ещё не существует или повреждён, возвращает пустой массив.
 *
 * @return array Список всех ранее сохранённых записей.
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
 * Сохраняет полный список записей обратно в JSON-файл.
 *
 * @param array $entries Список записей для сохранения.
 * @return void
 */
function saveEntries(array $entries): void
{
    file_put_contents(
        DATA_FILE,
        json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

// Обрабатываем запрос только если это действительно отправка формы методом POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$errors = validate($_POST);

if (!empty($errors)) {
    // Есть ошибки — возвращаем пользователя на форму вместе с сообщениями
    // и тем, что он уже успел ввести, чтобы не заполнять всё заново.
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: index.php');
    exit;
}

// Данные валидны — формируем новую запись.
$newEntry = [
    'id' => uniqid(),
    'title' => trim($_POST['title']),
    'entry_date' => $_POST['entry_date'],
    'mood' => $_POST['mood'],
    'energy_level' => (int)$_POST['energy_level'],
    'tags' => trim($_POST['tags'] ?? ''),
    'description' => trim($_POST['description']),
    'created_at' => date('Y-m-d H:i:s'),
];

$entries = loadEntries();
$entries[] = $newEntry;
saveEntries($entries);

$_SESSION['success_message'] = 'Запись успешно сохранена!';
header('Location: index.php');
exit;
