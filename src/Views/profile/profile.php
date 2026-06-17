<div class="profile-page">
    <h1>Профиль пользователя</h1>

    <div class="profile-card">
        <div class="profile-card__row">
            <span class="profile-card__label">Имя:</span>
            <span class="profile-card__value"><?= $this->escape($userModel->first_name) ?></span>
        </div>
        <div class="profile-card__row">
            <span class="profile-card__label">Фамилия:</span>
            <span class="profile-card__value"><?= $this->escape($userModel->last_name) ?></span>
        </div>
        <div class="profile-card__row">
            <span class="profile-card__label">Email:</span>
            <span class="profile-card__value"><?= $this->escape($userModel->email) ?></span>
        </div>
        <div class="profile-card__row">
            <span class="profile-card__label">Телефон:</span>
            <span class="profile-card__value"><?= $this->escape($userModel->phone) ?></span>
        </div>
        <div class="profile-card__row">
            <span class="profile-card__label">Город:</span>
            <span class="profile-card__value"><?= $city ? $this->escape($city->name) : 'Не указан' ?></span>
        </div>
        <div class="profile-card__row">
            <span class="profile-card__label">Роль:</span>
            <span class="profile-card__value"><?= $this->escape($user['role_name']) ?></span>
        </div>
        <div class="profile-card__row">
            <span class="profile-card__label">Объявлений:</span>
            <span class="profile-card__value"><?= (int) $adsCount ?></span>
        </div>
        <div class="profile-card__row">
            <span class="profile-card__label">Зарегистрирован:</span>
            <span class="profile-card__value"><?= $this->escape($userModel->created_at) ?></span>
        </div>
    </div>

    <div class="profile-actions">
        <a href="/profile/edit" class="btn btn--primary">Редактировать профиль</a>
        <a href="/profile/ads" class="btn">Мои объявления</a>
    </div>
</div>
