<section style="background:#fff; border-radius:10px; padding:1.5rem; box-shadow:0 1px 4px rgba(0,0,0,.08); margin-bottom:2rem;">
    <h2 style="margin-bottom:1rem; color:#2d6a4f;">➕ Добавить рецепт</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.8rem;">
            <label style="display:flex; flex-direction:column; gap:.3rem; font-size:.9rem;">
                Название
                <input type="text" name="name" required
                    style="padding:.5rem .7rem; border:1px solid #ccc; border-radius:6px; font-size:1rem;">
            </label>
            <label style="display:flex; flex-direction:column; gap:.3rem; font-size:.9rem;">
                Категория
                <input type="text" name="category" required
                    style="padding:.5rem .7rem; border:1px solid #ccc; border-radius:6px; font-size:1rem;">
            </label>
            <label style="display:flex; flex-direction:column; gap:.3rem; font-size:.9rem;">
                Время приготовления (мин)
                <input type="number" name="prep_time" min="1" required
                    style="padding:.5rem .7rem; border:1px solid #ccc; border-radius:6px; font-size:1rem;">
            </label>
            <label style="display:flex; flex-direction:column; gap:.3rem; font-size:.9rem;">
                Порции
                <input type="number" name="servings" min="1" value="2"
                    style="padding:.5rem .7rem; border:1px solid #ccc; border-radius:6px; font-size:1rem;">
            </label>
            <label style="display:flex; flex-direction:column; gap:.3rem; font-size:.9rem; grid-column:span 2;">
                Описание
                <textarea name="description" rows="2"
                    style="padding:.5rem .7rem; border:1px solid #ccc; border-radius:6px; font-size:1rem; resize:vertical;"></textarea>
            </label>
        </div>
        <button type="submit"
            style="margin-top:1rem; background:#2d6a4f; color:#fff; border:none; border-radius:6px;
                   padding:.6rem 1.4rem; font-size:1rem; cursor:pointer;">
            Добавить
        </button>
    </form>
</section>
