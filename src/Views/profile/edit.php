<h1>Редактирование профиля</h1>

<form action="/profile/edit" method="POST">
    <div class="form-group">
        <label for="first_name">Имя</label>
        <input type="text" name="first_name" id="first_name" value="<?= $this->escape($userModel->first_name) ?>" required>
    </div>

    <div class="form-group">
        <label for="last_name">Фамилия</label>
        <input type="text" name="last_name" id="last_name" value="<?= $this->escape($userModel->last_name) ?>" required>
    </div>

    <div class="form-group">
        <label for="phone">Телефон</label>
        <input type="text" name="phone" id="phone" value="<?= $this->escape($userModel->phone) ?>" required>
    </div>

    <div class="form-group">
        <label for="city_id">Город</label>
        <select name="city_id" id="city_id" required>
            <option value="">Выберите город</option>
            <?php foreach ($cities as $city): ?>
                <option value="<?= $city->city_id ?>" <?= $city->city_id === $userModel->city_id ? 'selected' : '' ?>>
                    <?= $this->escape($city->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <hr>

    <p class="form-hint">Оставьте поля пустыми, если не хотите менять пароль</p>

    <div class="form-group">
        <label for="password">Новый пароль</label>
        <input type="password" name="password" id="password">
    </div>

    <div class="form-group">
        <label for="password_confirmation">Подтверждение пароля</label>
        <input type="password" name="password_confirmation" id="password_confirmation">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/profile" class="btn">Отмена</a>
    </div>
</form>
