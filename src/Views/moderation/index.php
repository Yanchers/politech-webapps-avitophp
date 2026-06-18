<h1>Модерация объявлений</h1>

<?php if (empty($ads)): ?>
    <p>Нет объявлений на модерации.</p>
<?php else: ?>
    <div class="moderation-list">
        <?php foreach ($ads as $ad): ?>
            <div class="moderation-item">
                <div class="moderation-item__info">
                    <h2>
                        <a href="/ad/<?= $this->escape((string)$ad->ad_id) ?>">
                            <?= $this->escape($ad->title) ?>
                        </a>
                    </h2>
                    <p class="moderation-item__seller">
                        Продавец: <?= $this->escape($ad->seller_first_name ?? '') ?> <?= $this->escape($ad->seller_last_name ?? '') ?>
                        (<?= $this->escape($ad->seller_email ?? '') ?>)
                    </p>
                    <p class="moderation-item__meta">
                        Категория: <?= $this->escape($ad->category_name) ?> |
                        Город: <?= $this->escape($ad->city_name) ?> |
                        Цена: <?= $this->escape(number_format($ad->price, 0, '', ' ')) ?> ₽ |
                        Состояние: <?= $this->escape($ad->condition_name) ?>
                    </p>
                    <p class="moderation-item__date">
                        Создано: <?= $this->escape($ad->created_at) ?>
                    </p>
                </div>
                <div class="moderation-item__actions">
                    <form action="/moderation/<?= $ad->ad_id ?>/approve" method="POST" style="display:inline">
                        <button type="submit" class="btn btn--success">Одобрить</button>
                    </form>
                    <form action="/moderation/<?= $ad->ad_id ?>/reject" method="POST" style="display:inline">
                        <button type="submit" class="btn btn--danger">Отклонить</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
