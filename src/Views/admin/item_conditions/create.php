<h1>Создать состояние</h1>

<form action="/admin/item_conditions/store" method="POST">
    <div class="form-group">
        <label for="name">Название состояния</label>
        <input type="text" name="name" id="name" required maxlength="100">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Создать</button>
        <a href="/admin/item_conditions" class="btn">Отмена</a>
    </div>
</form>
