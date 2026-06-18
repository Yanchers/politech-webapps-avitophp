<h1>Редактировать состояние</h1>

<form action="/admin/item_conditions/<?= $item->item_condition_id ?>/update" method="POST">
    <div class="form-group">
        <label for="name">Название состояния</label>
        <input type="text" name="name" id="name" value="<?= $this->escape($item->name) ?>" required maxlength="100">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/admin/item_conditions" class="btn">Отмена</a>
    </div>
</form>
