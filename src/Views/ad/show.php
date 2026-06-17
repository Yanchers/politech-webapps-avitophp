<div class="ad-detail">
    <div class="ad-detail__gallery">
        <?php if (!empty($images)): ?>
            <div class="gallery-main">
                <img src="/<?= $this->escape($images[0]->image_path) ?>" alt="<?= $this->escape($ad->title) ?>" id="galleryMain">
            </div>
            <?php if (count($images) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($images as $image): ?>
                        <img src="/<?= $this->escape($image->image_path) ?>" alt="" class="gallery-thumb" onclick="document.getElementById('galleryMain').src=this.src">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="gallery-main gallery-main--empty">
                <p>Нет изображений</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="ad-detail__info">
        <h1 class="ad-detail__title"><?= $this->escape($ad->title) ?></h1>
        <div class="ad-detail__price"><?= number_format($ad->price, 0, ',', ' ') ?> ₽</div>

        <div class="ad-detail__meta">
            <p><strong>Категория:</strong> <?= $this->escape($ad->category_name) ?></p>
            <p><strong>Состояние:</strong> <?= $this->escape($ad->condition_name) ?></p>
            <p><strong>Город:</strong> <?= $this->escape($ad->city_name) ?></p>
            <p><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($ad->created_at)) ?></p>
            <p><strong>Статус:</strong> <?= $this->escape($ad->status_name) ?></p>
        </div>

        <div class="ad-detail__description">
            <h3>Описание</h3>
            <p><?= nl2br($this->escape($ad->description)) ?></p>
        </div>

        <?php if (isset($seller)): ?>
            <div class="ad-detail__seller">
                <h3>Продавец</h3>
                <p><?= $this->escape($seller->first_name . ' ' . $seller->last_name) ?></p>
                <p><a href="tel:<?= $this->escape($seller->phone) ?>"><?= $this->escape($seller->phone) ?></a></p>
            </div>
        <?php endif; ?>

        <div class="ad-detail__actions">
            <?php if (isset($user) && $user): ?>
                <?php if ($user['user_id'] === $ad->seller_id): ?>
                    <a href="/ad/<?= $ad->ad_id ?>/edit" class="btn">Редактировать</a>
                    <form action="/ad/<?= $ad->ad_id ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Удалить объявление?')">
                        <button type="submit" class="btn btn--danger">Удалить</button>
                    </form>
                <?php else: ?>
                    <?php if ($isFavorite): ?>
                        <form action="/favorites/remove/<?= $ad->ad_id ?>" method="POST" style="display:inline">
                            <button type="submit" class="btn btn--favorite btn--favorite--active">В избранном</button>
                        </form>
                    <?php else: ?>
                        <form action="/favorites/add/<?= $ad->ad_id ?>" method="POST" style="display:inline">
                            <button type="submit" class="btn btn--favorite">В избранное</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
