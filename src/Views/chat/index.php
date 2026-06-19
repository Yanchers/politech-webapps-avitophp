<div class="chat-list">
    <h1 class="chat-list__title">Мои сообщения</h1>

    <?php if (empty($chats)): ?>
        <div class="search-page__empty">
            <p>У вас пока нет сообщений</p>
            <a href="/" class="btn">На главную</a>
        </div>
    <?php else: ?>
        <div class="chat-list__items">
            <?php foreach ($chats as $chat): ?>
                <a href="/chat/<?= $chat['ad_id'] ?>/<?= $chat['other_user_id'] ?>" class="chat-list__item">
                    <div class="chat-list__item-avatar">
                        <?php if (!empty($chat['first_image'])): ?>
                            <img src="/<?= $this->escape($chat['first_image']) ?>" alt="">
                        <?php else: ?>
                            <div class="chat-list__item-no-img">📷</div>
                        <?php endif; ?>
                    </div>
                    <div class="chat-list__item-body">
                        <div class="chat-list__item-header">
                            <span class="chat-list__item-name"><?= $this->escape($chat['other_first_name'] . ' ' . $chat['other_last_name']) ?></span>
                            <span class="chat-list__item-date"><?= !empty($chat['created_at']) ? date('d.m.Y H:i', strtotime($chat['created_at'])) : '' ?></span>
                        </div>
                        <div class="chat-list__item-ad"><?= $this->escape($chat['ad_title']) ?></div>
                        <div class="chat-list__item-message"><?= $this->escape(mb_substr($chat['message'], 0, 100)) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
