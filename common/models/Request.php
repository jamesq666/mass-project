<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Request Model
 *
 * @property integer $id;
 * @property string $name;
 * @property string $email;
 * @property string $status;
 * @property string $message;
 * @property string $comment;
 * @property string $created_at;
 * @property string $updated_at;
 */
class Request extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{request}}';
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'email', 'message'], 'required'],
            [['name', 'email'], 'string', 'max' => 255],
            [['message', 'comment'], 'string'],
            [['email'], 'email'],
            ['status', 'in', 'range' => self::getMessageStatus()],
            [['created_at', 'updated_at'], 'datetime'],
        ];
    }

    /**
     * @return array[]
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя пользователя',
            'email' => 'Email пользователя',
            'status' => 'Статус',
            'message' => 'Сообщение пользователя',
            'comment' => 'Ответ ответственного лица',
            'created_at' => 'Время создания заявки',
            'updated_at' => 'Время ответа на заявку',
        ];
    }

    /**
     * @return array[]
     */
    public function getMessageStatus(): array
    {
        return [
            'Active',
            'Resolved',
        ];
    }
}
