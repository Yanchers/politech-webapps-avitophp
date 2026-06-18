<div style="text-align:center;padding:48px 24px;background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <div style="font-size:64px;margin-bottom:16px;">&#10004;</div>
    <h1 style="margin-bottom:8px;">Заказ оформлен!</h1>
    <p style="font-size:16px;color:#555;margin-bottom:24px;">
        Номер заказа: <strong style="color:#00aaff;"><?= $this->escape($order->order_number) ?></strong>
    </p>

    <div style="text-align:left;max-width:500px;margin:0 auto 24px;background:#f9f9f9;border-radius:8px;padding:16px;">
        <h3 style="font-size:14px;margin-bottom:12px;">Состав заказа</h3>
        <?php foreach ($order->items as $item): ?>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #e0e0e0;font-size:14px;">
                <span><?= $this->escape($item->ad_title) ?></span>
                <span style="font-weight:600;"><?= number_format($item->price_paid, 0, ',', ' ') ?> ₽</span>
            </div>
        <?php endforeach; ?>
        <div style="display:flex;justify-content:space-between;padding:12px 0 0;font-size:16px;font-weight:700;">
            <span>Итого</span>
            <span style="color:#00aaff;"><?= number_format($order->total_amount, 0, ',', ' ') ?> ₽</span>
        </div>
    </div>

    <p style="font-size:14px;color:#888;margin-bottom:24px;">
        Детали заказа и информация для связи с продавцами отправлены на вашу электронную почту.
    </p>

    <div style="display:flex;gap:12px;justify-content:center;">
        <a href="/orders" class="btn">Мои заказы</a>
        <a href="/" class="btn" style="background:#f0f0f0;color:#555;">На главную</a>
    </div>
</div>
