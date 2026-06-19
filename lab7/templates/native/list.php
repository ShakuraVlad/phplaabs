<section>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h2 style="color:#2d6a4f;">📋 Все рецепты</h2>
        <span class="badge badge-mode">Нативный PHP</span>
    </div>

    <?php if (empty($recipes)): ?>
        <p style="color:#888; text-align:center; padding:2rem;">Рецептов пока нет. Добавьте первый!</p>
    <?php else: ?>
        <div style="display:grid; gap:1rem;">
        <?php foreach ($recipes as $recipe): ?>
            <div style="background:#fff; border-radius:10px; padding:1.2rem 1.5rem;
                        box-shadow:0 1px 4px rgba(0,0,0,.08); display:flex; align-items:flex-start; gap:1rem;">
                <div style="flex:1;">
                    <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.3rem;">
                        <strong style="font-size:1.05rem;"><?= htmlspecialchars($recipe['name']) ?></strong>
                        <span class="badge"><?= htmlspecialchars($recipe['category']) ?></span>
                    </div>
                    <p style="color:#666; font-size:.9rem; margin-bottom:.5rem;">
                        <?= htmlspecialchars($recipe['description']) ?>
                    </p>
                    <div style="font-size:.85rem; color:#555;">
                        ⏱ <strong><?= htmlspecialchars(formatDuration($recipe['prep_time'])) ?></strong>
                        &nbsp;·&nbsp;
                        🍽 <?= (int)$recipe['servings'] ?> порц.
                    </div>
                </div>
                <form method="POST" style="flex-shrink:0;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$recipe['id'] ?>">
                    <button type="submit"
                        style="background:none; border:1px solid #e0e0e0; border-radius:6px;
                               padding:.4rem .8rem; cursor:pointer; color:#c0392b; font-size:.85rem;"
                        onclick="return confirm('Удалить рецепт?')">
                        🗑 Удалить
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
