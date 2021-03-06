<?php

use yii\helpers\Url;

/**
 * @var $this yii\web\View
 */
$this->title = 'Мобильная лаборатория - Главная';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index">
    <div class="body-content">
        <div class="panel panel-default">
            <div class="list-group">
                <a href="<?= Url::to(['/order']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Заказы</h4>
                    <p class="list-group-item-text">Создание и редактирование заказов</p>
                </a>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Контент</div>
            <div class="list-group">
                <a href="<?= Url::to(['/content/news']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Новости</h4>
                    <p class="list-group-item-text">Список новостей для сайта</p>
                </a>
                <a href="<?= Url::to(['/content/device']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Устройства</h4>
                    <p class="list-group-item-text">Устройства, которые мы ремонтируем с ценами и акциями</p>
                </a>
                <a href="<?= Url::to(['/content/service']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Услуги</h4>
                    <p class="list-group-item-text">Услуги, которые мы предоставляем по ремонту</p>
                </a>
                <a href="<?= Url::to(['/content/device-category']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Категории</h4>
                    <p class="list-group-item-text">Категории устройств по брендам. Древовидная структура</p>
                </a>
                <a href="<?= Url::to(['/content/vendor/']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Производители</h4>
                    <p class="list-group-item-text">Производители устройств</p>
                </a>
                <a href="<?= Url::to(['/content/price-list']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Прайс-листы</h4>
                    <p class="list-group-item-text">Загрузка и выгрузка цен по услугам на устройства в формате CSV</p>
                </a>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Настройки</div>
            <div class="list-group">
                <a href="<?= Url::to(['/settings/user']) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">Сотрудники компании</h4>
                    <p class="list-group-item-text">Редактирование профилей сотрудников</p>
                </a>
            </div>
        </div>
    </div>
</div>
