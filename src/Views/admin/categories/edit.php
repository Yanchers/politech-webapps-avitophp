<h1>Редактировать категорию</h1>

<form action="/admin/categories/<?= $item->category_id ?>/update" method="POST">
    <div class="form-group">
        <label for="name">Название категории</label>
        <input type="text" name="name" id="name" value="<?= $this->escape($item->name) ?>" required maxlength="100">
    </div>

    <div class="form-group">
        <label for="parent_id">Родительская категория</label>
        <select name="parent_id" id="parent_id">
            <option value="">— Корневая категория —</option>
            <?php foreach ($parents as $parent): ?>
                <?php if ($parent->category_id === $item->category_id) continue; ?>
                <option value="<?= $parent->category_id ?>" <?= $parent->category_id === $item->parent_id ? 'selected' : '' ?>>
                    <?= $this->escape($parent->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/admin/categories" class="btn">Отмена</a>
    </div>
</form>
