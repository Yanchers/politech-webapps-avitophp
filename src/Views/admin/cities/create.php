<h1>Создать город</h1>

<form action="/admin/cities/store" method="POST">
    <div class="form-group">
        <label for="name">Название города</label>
        <input type="text" name="name" id="name" required maxlength="100">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Создать</button>
        <a href="/admin/cities" class="btn">Отмена</a>
    </div>
</form>
