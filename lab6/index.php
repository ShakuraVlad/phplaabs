<?php
/**
 * Главная страница с формой добавления новой записи в "Дневник настроения".
 *
 * Если в сессии/GET есть результат предыдущей отправки (сообщение об успехе
 * или ошибки валидации), они отображаются над формой.
 */
session_start();

// Достаём сообщение, оставленное скриптом process.php, и сразу удаляем его,
// чтобы при обновлении страницы оно не показывалось снова.
$successMessage = $_SESSION['success_message'] ?? null;
$validationErrors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];

unset($_SESSION['success_message'], $_SESSION['errors'], $_SESSION['old_input']);

/**
 * Вспомогательная функция для безопасного вывода значений в HTML.
 * Защищает от XSS, экранируя спецсимволы.
 *
 * @param string $value Исходная строка.
 * @return string Экранированная строка, безопасная для вставки в HTML.
 */
function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дневник настроения — Новая запись</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>📔 Дневник настроения</h1>
    <nav>
        <a href="index.php">Новая запись</a> |
        <a href="view.php">Все записи</a>
    </nav>

    <?php if ($successMessage): ?>
        <div class="message success"><?= escape($successMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($validationErrors)): ?>
        <div class="message error">
            <strong>Исправьте следующие ошибки:</strong>
            <ul class="errors-list">
                <?php foreach ($validationErrors as $error): ?>
                    <li><?= escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="process.php" method="POST">

        <label for="title">Заголовок записи</label>
        <input
            type="text"
            id="title"
            name="title"
            required
            minlength="3"
            maxlength="100"
            placeholder="Например: Хороший день"
            value="<?= escape($oldInput['title'] ?? '') ?>"
        >

        <label for="entry_date">Дата</label>
        <input
            type="date"
            id="entry_date"
            name="entry_date"
            required
            value="<?= escape($oldInput['entry_date'] ?? date('Y-m-d')) ?>"
        >

        <label>Настроение</label>
        <div class="checkbox-group">
            <?php
            $moods = ['Радостно', 'Спокойно', 'Грустно', 'Тревожно', 'Злюсь'];
            $selectedMood = $oldInput['mood'] ?? '';
            foreach ($moods as $mood):
            ?>
                <label>
                    <input
                        type="radio"
                        name="mood"
                        value="<?= escape($mood) ?>"
                        <?= $selectedMood === $mood ? 'checked' : '' ?>
                        required
                    >
                    <?= escape($mood) ?>
                </label>
            <?php endforeach; ?>
        </div>

        <label for="energy_level">Уровень энергии (1 — мало, 10 — много)</label>
        <input
            type="number"
            id="energy_level"
            name="energy_level"
            min="1"
            max="10"
            required
            value="<?= escape($oldInput['energy_level'] ?? '5') ?>"
        >

        <label for="tags">Теги (через запятую)</label>
        <input
            type="text"
            id="tags"
            name="tags"
            maxlength="150"
            placeholder="например: работа, прогулка, друзья"
            value="<?= escape($oldInput['tags'] ?? '') ?>"
        >
        <div class="hint">Необязательное поле</div>

        <label for="description">Описание дня</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            required
            minlength="10"
            maxlength="2000"
            placeholder="Опишите, что произошло и почему вы себя так чувствуете..."
        ><?= escape($oldInput['description'] ?? '') ?></textarea>

        <button type="submit">Сохранить запись</button>
    </form>
</div>
</body>
</html>
