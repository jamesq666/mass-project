<?php

namespace api\modules\v1\models;

use common\models\Request;

/**
 * RequestUser Model
 *
 * @property string $name;
 * @property string $email;
 * @property string $message;
 */
class RequestUser extends Request
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['name', 'email'], 'string', 'max' => 255],
            [['message'], 'string'],
            [['email'], 'email'],
        ];
    }
}
