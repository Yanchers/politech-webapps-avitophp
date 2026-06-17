<h1>Избранное</h1>

<?php if (empty($favorites)): ?>
    <p>У вас пока нет избранных объявлений. <a href="/">Перейти на главную</a></p>
<?php else: ?>
    <div class="ads-list">
        <?php foreach ($favorites as $fav): ?>
            <div class="ad-card">
                <div class="ad-card__image">
                    <?php if (!empty($fav['first_image_path'])): ?>
                        <img src="/<?= $this->escape($fav['first_image_path']) ?>" alt="<?= $this->escape($fav['title']) ?>">
                    <?php else: ?>
                        <div class="ad-card__no-image">Нет фото</div>
                    <?php endif; ?>
                </div>
                <div class="ad-card__body">
                    <h3 class="ad-card__title">
                        <a href="/ad/<?= $fav['ad_id'] ?>"><?= $this->escape($fav['title']) ?></a>
                    </h3>
                    <div class="ad-card__price"><?= number_format($fav['price'], 0, ',', ' ') ?> ₽</div>
                    <div class="ad-card__meta">
                        <span><?= $this->escape($fav['city_name']) ?></span>
                        <span><?= date('d.m.Y', strtotime($fav['created_at'])) ?></span>
                        <span>Добавлено: <?= date('d.m.Y', strtotime($fav['added_at'])) ?></span>
                    </div>
                    <div class="ad-card__actions">
                        <form action="/favorites/remove/<?= $fav['ad_id'] ?>" method="POST" style="display:inline">
                            <button type="submit" class="btn btn--small btn--danger">Удалить из избранного</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
