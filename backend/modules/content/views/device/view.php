<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model common\models\ar\Device
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Устройства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить устройство?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'alias',
            [
                'label' => 'Изображение',
                'format' => 'raw',
                'value' => $model->getImageWebPath()
                    ? '<img src="' . Yii::$app->urlManagerFrontend->hostInfo . $model->getImageWebPath() . '" style="height:300px">'
                    : ' - ',
            ],
            'description:ntext',
            'image',
            'deviceCategory.name',
            'enabled:boolean',
            'vendor.name',
        ],
    ]) ?>

</div>
