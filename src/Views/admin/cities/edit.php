<h1>Редактировать город</h1>

<form action="/admin/cities/<?= $item->city_id ?>/update" method="POST">
    <div class="form-group">
        <label for="name">Название города</label>
        <input type="text" name="name" id="name" value="<?= $this->escape($item->name) ?>" required maxlength="100">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/admin/cities" class="btn">Отмена</a>
    </div>
</form>
