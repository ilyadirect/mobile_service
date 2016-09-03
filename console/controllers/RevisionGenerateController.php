<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Expression;
use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use common\models\ar\OrderService;
use common\models\ar\Service;
use common\models\ar\News;
use common\models\ar\Revision;
use common\models\ar\RevisionField;
use common\models\ar\RevisionTable;
use common\models\ar\RevisionValueType;
use common\models\ar\User;

class RevisionGenerateController extends Controller
{
    private $userId;

    public function init()
    {
        parent::init();
        $user = User::findByUsername('console@console.ru');

        if (is_null($user)) {
            $user = (new User([
                'first_name' => 'console',
                'last_name' => 'console',
                'auth_key' => '',
                'email' => 'console@console.ru',
                'phone' => '+77777777777',
                'enabled' => false,
            ]));
            $user->save(false);
        }

        $this->userId = $user->id;
    }

    /**
     * Создаёт начальную ревизию всех полей, которые не были проинициализированны
     *
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $tables = [
            Device::className(),
            DeviceAssign::className(),
            Order::className(),
            OrderPerson::className(),
            OrderService::className(),
            News::className(),
            Service::className(),
            User::className(),
        ];

        foreach ($tables as $table) {

            $models = $table::find()->all();
            $revisionTableId = RevisionTable::findOrCreateReturnScalar(['name' => $table::getTableSchema()->name]);
            foreach ($models as $model) {
                if (!isset($model->behaviors['revision'])) {
                    continue;
                }
                $attributes = $model->behaviors['revision']->attributes;

                $revisionTableName = Revision::tableName();
                $versionedFields = Revision::find()
                    ->select( RevisionField::tableName() . '.name')
                    ->joinWith('revisionField')
                    ->where([
                        $revisionTableName . '.revision_table_id' => $revisionTableId,
                        $revisionTableName . '.operation_type' => Revision::OPERATION_INSERT,
                        $revisionTableName . '.record_id' => $model->id,
                        RevisionField::tableName() . '.name' => $attributes,
                    ])
                    ->distinct()
                    ->column();

                $notVersionedFields = array_diff($attributes, $versionedFields);

                foreach ($notVersionedFields as $field) {
                    Yii::$app->db->createCommand()
                        ->insert($revisionTableName, [
                                'revision_table_id' => $revisionTableId,
                                'revision_field_id' => RevisionField::findOrCreateReturnScalar(['name' => $field]),
                                'record_id' => $model->id,
                                'revision_value_type_id' => RevisionValueType::findOrCreateReturnScalar(['name' => gettype($model->{$field})]),
                                'value' => $model->{$field},
                                'user_id' => $this->userId,
                                'operation_type' => Revision::OPERATION_INSERT,
                                'created_at' => new Expression('NOW()'),
                            ]
                        )->execute();

                }
            }
        }
    }
    
}
