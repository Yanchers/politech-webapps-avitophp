<div class="home">
    <div class="home__hero">
        <h1>Доска объявлений Avito PHP</h1>
        <p>Найдите нужные товары или разместите своё объявление</p>
        <form action="/search" method="GET" class="home__search-form">
            <div class="home__search-row">
                <input type="text" name="q" class="home__search-input" placeholder="Что вы ищете?" required>
                <button type="submit" class="btn btn--primary home__search-btn">Найти</button>
            </div>
        </form>
        <a href="/ad/create" class="btn btn--primary">Подать объявление</a>
    </div>

    <div class="home__categories">
        <h2>Категории</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="/search?category_id=<?= $cat->category_id ?>" class="category-card">
                    <?= $this->escape($cat->name) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="home__ads">
        <h2>Последние объявления</h2>
        <?php if (empty($ads)): ?>
            <p>Объявлений пока нет. <a href="/ad/create">Станьте первым!</a></p>
        <?php else: ?>
            <div class="ads-grid">
                <?php foreach ($ads as $ad):?>
                    <div class="ad-card">
                        <a href="/ad/<?= $ad->ad_id ?>" class="ad-card__link">
                            <div class="ad-card__image">
                                <?php if (!empty($ad->first_image_path)): ?>
                                    <img src="/<?= $this->escape($ad->first_image_path) ?>" alt="<?= $this->escape($ad->title) ?>">
                                <?php else: ?>
                                    <div class="ad-card__no-image">Нет фото</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-card__body">
                                <h3 class="ad-card__title"><?= $this->escape($ad->title) ?></h3>
                                <div class="ad-card__price"><?= number_format($ad->price, 0, ',', ' ') ?> ₽</div>
                                <div class="ad-card__meta">
                                    <span><?= $this->escape($ad->city_name) ?></span>
                                    <span><?= date('d.m.Y', strtotime($ad->created_at)) ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
