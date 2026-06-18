<h1>Корзина</h1>

<?php if (empty($items)): ?>
    <p>Корзина пуста. <a href="/">Перейти на главную</a></p>
<?php else: ?>
    <div class="cart-items">
        <?php foreach ($items as $item): ?>
            <div class="ad-card">
                <div class="ad-card__image">
                    <?php if (!empty($item['first_image_path'])): ?>
                        <img src="/<?= $this->escape($item['first_image_path']) ?>" alt="<?= $this->escape($item['title']) ?>">
                    <?php else: ?>
                        <div class="ad-card__no-image">Нет фото</div>
                    <?php endif; ?>
                </div>
                <div class="ad-card__body">
                    <h3 class="ad-card__title">
                        <a href="/ad/<?= $item['ad_id'] ?>"><?= $this->escape($item['title']) ?></a>
                    </h3>
                    <div class="ad-card__price"><?= number_format($item['price'], 0, ',', ' ') ?> ₽</div>
                    <div class="ad-card__meta">
                        <span><?= $this->escape($item['city_name']) ?></span>
                        <span><?= $this->escape($item['condition_name']) ?></span>
                    </div>
                    <?php if ($item['status_name'] === 'sold'): ?>
                        <div class="ad-card__status ad-card__status--sold">Продано</div>
                    <?php endif; ?>
                    <div class="ad-card__actions">
                        <form action="/cart/remove/<?= $item['ad_id'] ?>" method="POST" style="display:inline">
                            <button type="submit" class="btn btn--small btn--danger">Удалить</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="cart-summary" style="margin-top:24px;padding:20px;background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="font-size:20px;font-weight:700;margin-bottom:16px;">
            Итого: <span style="color:#00aaff;"><?= number_format($total, 0, ',', ' ') ?> ₽</span>
        </div>
        <a href="/order/create" class="btn" style="font-size:16px;padding:12px 32px;">Оформить заказ</a>
    </div>
<?php endif; ?>
