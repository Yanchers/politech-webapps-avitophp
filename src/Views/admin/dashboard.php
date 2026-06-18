<h1>Админ-панель</h1>

<div class="admin-stats">
    <a href="/admin/users" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['users']) ?></div>
        <div class="admin-stat-card__label">Пользователи</div>
    </a>
    <a href="/admin/advertisements" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['ads']) ?></div>
        <div class="admin-stat-card__label">Объявления</div>
    </a>
    <a href="/admin/cities" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['cities']) ?></div>
        <div class="admin-stat-card__label">Города</div>
    </a>
    <a href="/admin/categories" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['categories']) ?></div>
        <div class="admin-stat-card__label">Категории</div>
    </a>
    <a href="/admin/item_conditions" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['conditions']) ?></div>
        <div class="admin-stat-card__label">Состояния</div>
    </a>
    <a href="/admin/ad_chat_messages" class="admin-stat-card">
        <div class="admin-stat-card__number"><?= $this->escape((string)$stats['messages']) ?></div>
        <div class="admin-stat-card__label">Сообщения</div>
    </a>
</div>
