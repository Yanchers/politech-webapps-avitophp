<h1>Редактировать пользователя</h1>

<form action="/admin/users/<?= $item->user_id ?>/update" method="POST">
    <div class="form-row">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= $this->escape($item->email) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="text" name="phone" id="phone" value="<?= $this->escape($item->phone) ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="password">Новый пароль (оставьте пустым, чтобы не менять)</label>
        <input type="password" name="password" id="password">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="first_name">Имя</label>
            <input type="text" name="first_name" id="first_name" value="<?= $this->escape($item->first_name) ?>" required maxlength="50">
        </div>
        <div class="form-group">
            <label for="last_name">Фамилия</label>
            <input type="text" name="last_name" id="last_name" value="<?= $this->escape($item->last_name) ?>" required maxlength="50">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="role_id">Роль</label>
            <select name="role_id" id="role_id">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role->role_id ?>" <?= $role->role_id === $item->role_id ? 'selected' : '' ?>><?= $this->escape($role->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="city_id">Город</label>
            <select name="city_id" id="city_id">
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city->city_id ?>" <?= $city->city_id === $item->city_id ? 'selected' : '' ?>><?= $this->escape($city->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/admin/users" class="btn">Отмена</a>
    </div>
</form>
