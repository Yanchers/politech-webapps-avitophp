<div class="chat-show">
    <div class="chat-show__header">
        <a href="/chat" class="chat-show__back">← Все сообщения</a>
        <div class="chat-show__info">
            <h1>Чат с <?= $this->escape($otherUser->first_name . ' ' . $otherUser->last_name) ?></h1>
            <p class="chat-show__ad">
                Объявление: <a href="/ad/<?= $ad->ad_id ?>"><?= $this->escape($ad->title) ?></a>
                — <?= number_format($ad->price, 0, ',', ' ') ?> ₽
            </p>
        </div>
    </div>

    <div class="chat-show__messages" id="chatMessages">
        <?php if (empty($messages)): ?>
            <div class="chat-show__empty">Напишите первое сообщение</div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="chat-message <?= $msg->sender_id === $user['user_id'] ? 'chat-message--own' : 'chat-message--other' ?>">
                    <div class="chat-message__text"><?= nl2br($this->escape($msg->message)) ?></div>
                    <div class="chat-message__time"><?= $msg->created_at ? date('d.m.Y H:i', strtotime($msg->created_at)) : '' ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form action="/chat/<?= $ad->ad_id ?>/<?= $otherUser->user_id ?>" method="POST" class="chat-show__form">
        <textarea name="message" class="chat-show__input" placeholder="Напишите сообщение..." rows="3" required></textarea>
        <button type="submit" class="btn">Отправить</button>
    </form>
</div>
