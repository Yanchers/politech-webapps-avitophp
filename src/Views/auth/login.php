<h1>Вход</h1>
<form action="/login" method="POST">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= $this->escape($_SESSION['old_input']['email'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required>
    </div>
    <button type="submit" class="btn">Войти</button>
</form>
<p>Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
