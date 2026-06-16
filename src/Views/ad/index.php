<h1>Мои объявления</h1>

<?php if (empty($ads)): ?>
    <p>У вас пока нет объявлений. <a href="/ad/create">Создать первое объявление</a></p>
<?php else: ?>
    <div class="ads-list">
        <?php foreach ($ads as $ad): ?>
            <div class="ad-card">
                <div class="ad-card__image">
                    <?php
                    $firstImage = (new \App\Repositories\AdvertisementRepository())->getImages($ad->ad_id);
                    if (!empty($firstImage)):
                    ?>
                        <img src="/<?= $this->escape($firstImage[0]->image_path) ?>" alt="<?= $this->escape($ad->title) ?>">
                    <?php else: ?>
                        <div class="ad-card__no-image">Нет фото</div>
                    <?php endif; ?>
                </div>
                <div class="ad-card__body">
                    <h3 class="ad-card__title">
                        <a href="/ad/<?= $ad->ad_id ?>"><?= $this->escape($ad->title) ?></a>
                    </h3>
                    <div class="ad-card__price"><?= number_format($ad->price, 0, ',', ' ') ?> ₽</div>
                    <div class="ad-card__meta">
                        <span class="ad-card__status ad-card__status--<?= $this->escape($ad->status_name) ?>">
                            <?= $this->escape($ad->status_name) ?>
                        </span>
                        <span><?= $this->escape($ad->city_name) ?></span>
                        <span><?= date('d.m.Y', strtotime($ad->created_at)) ?></span>
                    </div>
                    <div class="ad-card__actions">
                        <a href="/ad/<?= $ad->ad_id ?>/edit" class="btn btn--small">Редактировать</a>
                        <form action="/ad/<?= $ad->ad_id ?>/delete" method="POST" style="display:inline" onsubmit="return confirm('Удалить объявление?')">
                            <button type="submit" class="btn btn--small btn--danger">Удалить</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
