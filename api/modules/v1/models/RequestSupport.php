<?php

namespace api\modules\v1\models;

use common\models\Request;

/**
 * RequestSupport Model
 *
 * @property integer $id;
 * @property string $status;
 * @property string $comment;
 */
class RequestSupport extends Request
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['comment'], 'required'],
            [['comment'], 'string'],
            [['id'], 'integer'],
            ['status', 'in', 'range' => self::getMessageStatus()],
        ];
    }
}
