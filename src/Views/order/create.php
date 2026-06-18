<h1>Оформление заказа</h1>

<div style="background:#fff;border-radius:8px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:24px;">
    <h2 style="font-size:18px;margin-bottom:16px;">Состав заказа</h2>

    <?php foreach ($items as $item): ?>
        <div style="display:flex;align-items:center;gap:16px;padding:12px 0;border-bottom:1px solid #f0f0f0;">
            <div style="width:80px;height:60px;overflow:hidden;border-radius:4px;background:#f0f0f0;flex-shrink:0;">
                <?php if (!empty($item['first_image_path'])): ?>
                    <img src="/<?= $this->escape($item['first_image_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#999;font-size:11px;">Нет фото</div>
                <?php endif; ?>
            </div>
            <div style="flex:1;">
                <div style="font-weight:600;"><?= $this->escape($item['title']) ?></div>
                <div style="font-size:13px;color:#888;">Продавец: <?= $this->escape($item['seller_first_name'] . ' ' . $item['seller_last_name']) ?></div>
            </div>
            <div style="font-size:18px;font-weight:700;color:#00aaff;white-space:nowrap;">
                <?= number_format($item['price'], 0, ',', ' ') ?> ₽
            </div>
        </div>
    <?php endforeach; ?>

    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 0 0;font-size:20px;font-weight:700;">
        <span>Итого</span>
        <span style="color:#00aaff;"><?= number_format($total, 0, ',', ' ') ?> ₽</span>
    </div>
</div>

<form action="/order/create" method="POST">
    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn" style="font-size:16px;padding:12px 32px;">Подтвердить заказ</button>
        <a href="/cart" class="btn" style="background:#f0f0f0;color:#555;font-size:16px;padding:12px 32px;">Вернуться в корзину</a>
    </div>
</form>
