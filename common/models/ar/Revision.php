<?php

namespace common\models\ar;

use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use common\components\db\ActiveRecord;
use common\helpers\SystemHelper;

/**
 * This is the model class for table "revision".
 *
 * @property integer $id
 * @property integer $revision_table_id
 * @property integer $revision_field_id
 * @property integer $record_id
 * @property integer $revision_value_type_id
 * @property string $value
 * @property integer $user_id
 * @property string $created_at
 *
 * @property User $user
 * @property RevisionField $revisionField
 * @property RevisionTable $revisionTable
 * @property RevisionValueType $revisionValueType
 */
class Revision extends \yii\db\ActiveRecord
{
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            'blamable' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
                ],
                'value' => function() {
                    // Если изменения в БД происходят через консольный скрипт, то пользователь console
                    if (SystemHelper::isConsole()) {
                        $value = SystemHelper::getConsoleUserId();
                    } else {
                        $user = Yii::$app->user;
                        $value = $user && !$user->isGuest ? $user->id : null;
                    }

                    return $value;
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%revision}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'revision_table_id' => 'Таблица по ревизии',
            'revision_field_id' => 'Поле по ревизии',
            'record_id' => 'id записи в изменённой таблице',
            'revision_value_type_id' => 'Тип данных у значения',
            'value' => 'Новое значение',
            'user_id' => 'Пользователь, изменивший значение',
            'created_at' => 'Время изменения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionField()
    {
        return $this->hasOne(RevisionField::className(), ['id' => 'revision_field_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionTable()
    {
        return $this->hasOne(RevisionTable::className(), ['id' => 'revision_table_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionValueType()
    {
        return $this->hasOne(RevisionValueType::className(), ['id' => 'revision_value_type_id']);
    }

    /**
     * Невозможно изменять ревизию
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        return false;
    }

    /**
     * Значение поле у таблицы в определённый момент времени.
     * Если время неуказано или указано некорректно вернётся текущее значение.
     *
     * @param string $table Название таблицы
     * @param integer $recordId id записи в таблице
     * @param string $field название поле таблицы
     * @param string $dateTime время
     *
     * @throws Exception не найдено время создания в ревизии
     * @return bool|mixed|string значение
     */
    public static function getValue($table, $recordId, $field, $dateTime = null)
    {
        if (!strtotime($dateTime)) {
            $value= (new Query())
                ->select($field)
                ->from($table)
                ->where(['id' => $recordId])
                ->scalar();
            return $value;
        }
        $tableId = RevisionTable::find()
            ->select('id')
            ->where(['name' => $table])
            ->scalar();

        $fieldId = RevisionField::find()
            ->select('id')
            ->where(['name' => $field])
            ->scalar();

        /** @var Revision $revision  Ревизии только на Update */
        $revision = self::find()
            ->where([
                'revision_table_id' => $tableId,
                'revision_field_id' => $fieldId,
                'record_id' => $recordId,
            ])
            ->andWhere(['<', 'created_at', $dateTime])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        if ($revision) {
            $revisionValueType = RevisionValueType::find()
                ->select('name')
                ->where(['id' => $revision->revision_value_type_id])
                ->scalar();
    
            $value = $revision->value;
            if (isset(RevisionValueType::$castFunctions[$revisionValueType])) {
                $value = call_user_func(RevisionValueType::$castFunctions[$revisionValueType], $revision->value);
            }
        } else {
            /** @var RevisionRecord $revisionRecord */
            $revisionRecord = RevisionRecord::find()
                ->select('value')
                ->where([
                    'revision_table_id' => $tableId,
                    'record_id' => $recordId,
                ])->one();
            if (!$revisionRecord) {
                throw new Exception('RevisionRecord was not initialized: table = '
                    . $table . '. record_id = ' . $recordId);
            }
            
            $value = Json::decode($revisionRecord->value)[$field];
        }

        return $value;
    }
    
}
