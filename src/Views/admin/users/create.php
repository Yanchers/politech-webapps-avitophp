<h1>Создать пользователя</h1>

<form action="/admin/users/store" method="POST">
    <div class="form-row">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="text" name="phone" id="phone">
        </div>
    </div>

    <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="first_name">Имя</label>
            <input type="text" name="first_name" id="first_name" required maxlength="50">
        </div>
        <div class="form-group">
            <label for="last_name">Фамилия</label>
            <input type="text" name="last_name" id="last_name" required maxlength="50">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="role_id">Роль</label>
            <select name="role_id" id="role_id">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role->role_id ?>" <?= $role->role_id === 1 ? 'selected' : '' ?>><?= $this->escape($role->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="city_id">Город</label>
            <select name="city_id" id="city_id">
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city->city_id ?>"><?= $this->escape($city->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Создать</button>
        <a href="/admin/users" class="btn">Отмена</a>
    </div>
</form>
