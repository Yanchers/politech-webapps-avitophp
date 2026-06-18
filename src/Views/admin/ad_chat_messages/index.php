<h1>Сообщения чата</h1>

<div class="admin-toolbar">
    <a href="/admin/ad_chat_messages/create" class="btn">+ Создать</a>
</div>

<?php if (empty($items)): ?>
    <p>Нет сообщений.</p>
<?php else: ?>
    <form action="/admin/ad_chat_messages/batch-delete" method="POST" class="admin-batch-form">
        <table class="admin-table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="admin-check-all"></th>
                    <th>ID</th>
                    <th>Объявление</th>
                    <th>Отправитель</th>
                    <th>Получатель</th>
                    <th>Сообщение</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?= $item['ad_chat_message_id'] ?>" class="admin-check-item"></td>
                        <td><?= $item['ad_chat_message_id'] ?></td>
                        <td><?= $this->escape($item['ad_title']) ?></td>
                        <td><?= $this->escape($item['sender_first_name'] . ' ' . $item['sender_last_name']) ?></td>
                        <td><?= $this->escape($item['receiver_first_name'] . ' ' . $item['receiver_last_name']) ?></td>
                        <td class="admin-cell-preview"><?= $this->escape(mb_substr($item['message'], 0, 100)) ?><?= mb_strlen($item['message']) > 100 ? '...' : '' ?></td>
                        <td class="admin-actions">
                            <a href="/admin/ad_chat_messages/<?= $item['ad_chat_message_id'] ?>/edit" class="btn btn--small">Ред.</a>
                            <a href="/admin/ad_chat_messages/<?= $item['ad_chat_message_id'] ?>/delete" class="btn btn--small btn--danger" onclick="return confirm('Удалить?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn--danger btn--small" onclick="return confirm('Удалить выбранные?')">Удалить выбранные</button>
    </form>
<?php endif; ?>
