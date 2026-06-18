<h1>Редактировать сообщение</h1>

<form action="/admin/ad_chat_messages/<?= $item->ad_chat_message_id ?>/update" method="POST">
    <div class="form-group">
        <label for="ad_id">Объявление</label>
        <select name="ad_id" id="ad_id" required>
            <option value="">Выберите объявление</option>
            <?php foreach ($ads as $ad): ?>
                <option value="<?= $ad['ad_id'] ?>" <?= (int)$ad['ad_id'] === $item->ad_id ? 'selected' : '' ?>>#<?= $ad['ad_id'] ?> — <?= $this->escape($ad['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="sender_id">Отправитель</label>
            <select name="sender_id" id="sender_id" required>
                <option value="">Выберите отправителя</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>" <?= (int)$user['user_id'] === $item->sender_id ? 'selected' : '' ?>><?= $this->escape($user['email'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="receiver_id">Получатель</label>
            <select name="receiver_id" id="receiver_id" required>
                <option value="">Выберите получателя</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>" <?= (int)$user['user_id'] === $item->receiver_id ? 'selected' : '' ?>><?= $this->escape($user['email'] . ' (' . $user['first_name'] . ' ' . $user['last_name'] . ')') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="message">Сообщение</label>
        <textarea name="message" id="message" rows="4" required><?= $this->escape($item->message) ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Сохранить</button>
        <a href="/admin/ad_chat_messages" class="btn">Отмена</a>
    </div>
</form>
