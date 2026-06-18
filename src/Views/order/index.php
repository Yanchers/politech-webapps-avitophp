<h1>Мои заказы</h1>

<?php if (empty($orders)): ?>
    <p>У вас пока нет заказов. <a href="/">Перейти на главную</a></p>
<?php else: ?>
    <div class="ads-list">
        <?php foreach ($orders as $order): ?>
            <?php $itemsCount = count($order->items); ?>
            <a href="/order/<?= $this->escape($order->order_number) ?>" style="text-decoration:none;color:inherit;">
                <div class="ad-card">
                    <div class="ad-card__body">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:16px;font-weight:600;margin-bottom:4px;">
                                    Заказ №<?= $this->escape($order->order_number) ?>
                                </div>
                                <div class="ad-card__meta">
                                    <span><?= date('d.m.Y H:i', strtotime($order->created_at)) ?></span>
                                    <span>Товаров: <?= $itemsCount ?> шт.</span>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div class="ad-card__price"><?= number_format($order->total_amount, 0, ',', ' ') ?> ₽</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
