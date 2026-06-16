<h1>Регистрация</h1>
<form action="/register" method="POST">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= $this->escape($_SESSION['old_input']['email'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="phone">Телефон</label>
        <input type="text" name="phone" id="phone" value="<?= $this->escape($_SESSION['old_input']['phone'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="first_name">Имя</label>
        <input type="text" name="first_name" id="first_name" value="<?= $this->escape($_SESSION['old_input']['first_name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="last_name">Фамилия</label>
        <input type="text" name="last_name" id="last_name" value="<?= $this->escape($_SESSION['old_input']['last_name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="city_id">Город</label>
        <select name="city_id" id="city_id" required>
            <option value="">Выберите город</option>
            <?php foreach ($cities as $city): ?>
                <option value="<?= $city->city_id ?>" <?= (isset($_SESSION['old_input']['city_id']) && (int)$_SESSION['old_input']['city_id'] === $city->city_id) ? 'selected' : '' ?>>
                    <?= $this->escape($city->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div class="form-group">
        <label for="password_confirmation">Подтверждение пароля</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
    </div>
    <button type="submit" class="btn">Зарегистрироваться</button>
</form>
<p>Уже есть аккаунт? <a href="/login">Войти</a></p>
