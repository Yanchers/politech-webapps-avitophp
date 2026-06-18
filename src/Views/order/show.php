<h1>Заказ №<?= $this->escape($order->order_number) ?></h1>

<div style="background:#fff;border-radius:8px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f0f0f0;">
        <div>
            <div style="font-size:14px;color:#888;">Номер заказа</div>
            <div style="font-size:18px;font-weight:600;"><?= $this->escape($order->order_number) ?></div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:14px;color:#888;">Дата оформления</div>
            <div style="font-size:16px;"><?= date('d.m.Y H:i', strtotime($order->created_at)) ?></div>
        </div>
    </div>

    <h2 style="font-size:16px;margin-bottom:12px;">Товары в заказе</h2>

    <?php foreach ($order->items as $item): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f0f0f0;">
            <div>
                <div style="font-weight:600;"><?= $this->escape($item->ad_title) ?></div>
                <div style="font-size:13px;color:#888;">
                    Продавец: <?= $this->escape($item->seller_first_name . ' ' . $item->seller_last_name) ?>
                    &middot; <?= $this->escape($item->seller_email) ?>
                    &middot; <?= $this->escape($item->seller_phone) ?>
                </div>
            </div>
            <div style="font-size:18px;font-weight:700;color:#00aaff;white-space:nowrap;">
                <?= number_format($item->price_paid, 0, ',', ' ') ?> ₽
            </div>
        </div>
    <?php endforeach; ?>

    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 0 0;font-size:20px;font-weight:700;">
        <span>Итого</span>
        <span style="color:#00aaff;"><?= number_format($order->total_amount, 0, ',', ' ') ?> ₽</span>
    </div>
</div>

<div style="display:flex;gap:12px;">
    <a href="/orders" class="btn">Все заказы</a>
    <a href="/" class="btn" style="background:#f0f0f0;color:#555;">На главную</a>
</div>
