<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\widgets\MaskedInput;
use frontend\assets\AppAsset;
use frontend\models\DeviceOrderForm;
use common\models\ar\Device;
use dosamigos\datetimepicker\DateTimePicker;

/**
 * @var \yii\web\View $this
 * @var \common\models\ar\Device $model
 * @var \frontend\models\OrderWithDiscountForm $orderWithDiscount Форма заказа со скидкой
 *
 */
$modalFormModel = new DeviceOrderForm();

// TODO: СКЛОНЕНИЕ РУССКИХ СЛОВ, УБРАТЬ ХАРДКОД!
$name = $model->name;
if ($name == 'Компьютер') {
    $name = 'Компьютеров';
} else if ($name == 'Ноутбук') {
    $name = 'Ноутбуков';
}
// Конец
$this->title = 'Ремонт ' .  $name;
$bundle = AppAsset::register($this);
$baseUrl = $bundle->baseUrl;

if ($model->deviceCategory) {
    $this->params['breadcrumbs'][] = [
        'label' => $model->deviceCategory->name,
        'url' => [
            'site/category',
            'alias' => $model->deviceCategory->alias,
        ]
    ];
}

if ($model->vendor) {
    $vendorBreadcrumb = ['label' => $model->vendor->name, 'url' => [
        'site/vendor',
        'vendorAlias' => $model->vendor->alias,
    ]];
    if ($model->deviceCategory) {
        $vendorBreadcrumb['url']['categoryAlias'] = $model->deviceCategory->alias;
    }
    $this->params['breadcrumbs'][] = $vendorBreadcrumb;
}
$this->params['breadcrumbs'][] = ['label' => $model->name];
$this->registerCssFile($baseUrl . '/css/device.css');
$this->registerJsFile($baseUrl . '/js/device.js', ['depends' => \yii\web\JqueryAsset::className()]);
?>
<div class="wr_sevis_ttle">
    <div class="sevis_ttle">
        <div class = "row">
            <?php if (empty($model->description)): ?>
                <div class = "col-xs-12">
                    <div class = "col-xs-12">
                        <img class="device-image"
                             style="max-width: 150px"
                             src="<?= Yii::$app->storage->getUrl($model->image_name, Device::IMAGE_SAVE_FOLDER); ?>"
                             alt="<?= Html::encode($model->name); ?>" />
                        <a
                            data-toggle="modal"
                            data-target="#order-service"
                            data-device-id="<?= $model->id; ?>"
                            data-device-name="<?= $model->name; ?>"
                            class="device-order-button">
                            Заказать ремонт
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($model->image_name): ?>
                    <div class = "col-xs-12 col-sm-3 col-md-2 col-lg-2">
                        <img class="device-image" src="<?= Yii::$app->storage->getUrl($model->image_name, Device::IMAGE_SAVE_FOLDER); ?>" alt="<?= Html::encode($model->name); ?>" />
                    </div>
                <?php endif; ?>
                <div class = "col-xs-12 col-sm-9 col-md-10 col-lg-10">
                    <div class="device-description"><?= HtmlPurifier::process($model->description); ?></div>
                    <a
                        data-toggle="modal"
                        data-target="#order-service"
                        data-device-id="<?= $model->id; ?>"
                        data-device-name="<?= $model->name; ?>"
                        class="device-order-button">Заказать ремонт</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->render('_advantages-section'); ?>
<?php if (!empty($model->deviceAssigns)): ?>
    <div class="table">
        <table id="device-table">
            <tr class="tr_one">
                <td>
                    <h3>Наименование услуги</h3>
                </td>
                <td>
                    <h3>СТОИМОСТЬ</h3>
                </td>
                <td class="hidden-md hidden-sm hidden-xs">
                    <h3>Гарантии</h3>
                </td>
            </tr>
            <?php foreach ($model->deviceAssigns as $assign): ?>
                <tr>
                    <td>
                        <p class="ligth"><?= Html::encode($assign->service->name); ?></p>
                        <?php if ($assign->service->small_description): ?>
                            <a class="detailed" data-toggle="collapse" data-target="#service-description-<?= $assign->id; ?>">Подробнее</a>
                            <div id="service-description-<?= $assign->id; ?>" class="collapse bg_green"><?= Html::encode($assign->service->small_description); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <p class="green"><?= number_format($assign->price, 0, '.', ' '); ?> руб.</p>
                        <a
                            data-toggle="modal"
                            data-target="#order-service"
                            data-device-id="<?= $model->id; ?>"
                            data-device-name="<?= $model->name; ?>"
                            data-device-assign-id="<?= $assign->id; ?>"
                            data-service-name="<?= $assign->service->name; ?>"
                            data-price="<?= number_format($assign->price, 0, '.', ' '); ?>"
                            class="price">заказать сейчас</a>
                    </td>
                    <td class="hidden-md hidden-sm hidden-xs">
                        <p class="ligth">6 мес.</p>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>
<div class="wr_report wr_report4">
    <div class="report">
        <img src="<?= $baseUrl; ?>/images/repor_pos.png" alt="" class="repor_img"/>
        <?php if (empty($model->deviceAssigns)) : ?>
            <h2>Оформите заявку на ремонт со скидкой 5%</h2>
        <?php else: ?>
            <h2>Не нашли нужную услугу? Оформите заявку со скидкой 5%</h2>
        <?php endif; ?>
        <?php $form = ActiveForm::begin(['id' => 'device-order-discount-form']); ?>
            <?= Html::activeHiddenInput($orderWithDiscount, 'device_provider_id', ['value' => $model->id]); ?>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" >
                    <?= $form->field($orderWithDiscount, 'first_name', ['options' => ['style' => 'margin-top:30px']])->textInput(['maxlength' => true]); ?>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <?= $form->field($orderWithDiscount, 'phone', ['options' => ['style' => 'margin-top:30px']])->widget(MaskedInput::className(), [
                        'mask' => '+7 (999) 999-99-99',
                    ]); ?>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                    <?= $form->field($orderWithDiscount, 'time_to', ['options' => ['style' => 'margin-top:30px']])->widget(DateTimePicker::className(), [
                        'language' => 'ru',
                        'size' => 'ms',
                        'template' => '{input}',
                        'pickButtonIcon' => 'glyphicon glyphicon-time',
                        'clientOptions' => [
                            'startView' => 1,
                            'minView' => 0,
                            'maxView' => 2,
                            'autoclose' => true,
                            'linkFormat' => 'HH:ii P', // if inline = true
                            'format' => 'HH:ii',
                            'todayBtn' => true,
                            'minuteStep' => 15,
                            'todayHighlight' => true,
                        ],
                    ]); ?>
                </div>
            </div>
            <?= Html::submitButton('оформить заявку'); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?= $this->render('_about-text'); ?>
<?= $this->render('_courier-delivery-banner'); ?>
<?= $this->render('_why-we-text'); ?>

<!-- Модальная форма заказа -->
<div id="order-service" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Заявка на ремонт</h4>
            </div>
            <div class="modal-body">
                <div class="well well-sm device-modal-description"></div>
                <?php $form = ActiveForm::begin(['action' => '/site/device-order/', 'id' => 'device-order-form']); ?>
                <?= Html::activeHiddenInput($modalFormModel, 'device_provider_id'); ?>
                <?= Html::activeHiddenInput($modalFormModel, 'device_assign_id'); ?>
                <?= $form->field($modalFormModel, 'first_name')->textInput(['maxlength' => true, 'placeholder' => 'Имя *'])->label(false); ?>
                <?= $form->field($modalFormModel, 'phone')->widget(MaskedInput::className(), [
                    'mask' => '+7 (999) 999-99-99'
                ])->textInput(['placeholder' => 'Телефон *'])->label(false); ?>
                <?= $form->field($modalFormModel, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Email'])->label(false); ?>
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-success', 'style' => 'display:block;background:#00C962;margin:auto;']); ?>
                <?php $form->end(); ?>
            </div>
        </div>
    </div>
</div>
