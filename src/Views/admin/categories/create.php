<h1>Создать категорию</h1>

<form action="/admin/categories/store" method="POST">
    <div class="form-group">
        <label for="name">Название категории</label>
        <input type="text" name="name" id="name" required maxlength="100">
    </div>

    <div class="form-group">
        <label for="parent_id">Родительская категория</label>
        <select name="parent_id" id="parent_id">
            <option value="">— Корневая категория —</option>
            <?php foreach ($parents as $parent): ?>
                <option value="<?= $parent->category_id ?>"><?= $this->escape($parent->name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Создать</button>
        <a href="/admin/categories" class="btn">Отмена</a>
    </div>
</form>
