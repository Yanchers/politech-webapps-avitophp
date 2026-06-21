<h1>Объявления</h1>

<div class="admin-toolbar">
    <a href="/admin/advertisements/create" class="btn">+ Создать</a>
</div>

<?php if (empty($items)): ?>
    <p>Нет объявлений.</p>
<?php else: ?>
    <form action="/admin/advertisements/batch-delete" method="POST" class="admin-batch-form">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="admin-check-all"></th>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Продавец</th>
                    <th>Цена</th>
                    <th>Категория</th>
                    <th>Город</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?= $item->ad_id ?>" class="admin-check-item"></td>
                        <td><?= $item->ad_id ?></td>
                        <td><a href="/ad/<?= $item->ad_id ?>"><?= $this->escape($item->title) ?></a></td>
                        <td><?= $this->escape($item->seller_first_name . ' ' . $item->seller_last_name) ?></td>
                        <td><?= number_format($item->price, 0, '', ' ') ?> ₽</td>
                        <td><?= $this->escape($item->category_name) ?></td>
                        <td><?= $this->escape($item->city_name) ?></td>
                        <td><?= $this->escape($item->status_name) ?></td>
                        <td class="admin-actions">
                            <a href="/admin/advertisements/<?= $item->ad_id ?>/edit" class="btn btn--small">Ред.</a>
                            <a href="/admin/advertisements/<?= $item->ad_id ?>/delete" class="btn btn--small btn--danger" onclick="return confirm('Удалить?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn--danger btn--small" onclick="return confirm('Удалить выбранные?')">Удалить выбранные</button>
    </form>
<?php endif; ?>