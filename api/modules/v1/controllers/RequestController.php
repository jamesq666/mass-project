<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\RequestSupport;
use api\modules\v1\models\RequestUser;
use common\models\Request;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Request Controller API
 */
class RequestController extends Controller
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * @return string[]
     * @throws Throwable
     */
    public function actionPost(): array
    {
        $model = new RequestUser();

        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            return ['message' => 'Request created successfully!'];
        }

        return ['message' => 'Request not created!'];
    }

    /**
     * @return string[]|array
     * @throws InvalidConfigException
     */
    public function actionPut()
    {
        $params = Yii::$app->request->getBodyParams();

        if (!empty($params)) {
            if (!isset($params['id'])) {
                return ['message' => 'Request number not provided!'];
            }

            $model = RequestSupport::findOne($params['id']);

            if (!$model) {
                return ['message' => 'Request not found!'];
            }

            if (!isset($params['comment'])) {
                return ['message' => 'Changes not saved! Add a comment!'];
            }

            $model->comment = $params['comment'];
            $model->status = 'Resolved';

            if ($model->save()) {
                self::SendEmail($model);
                return ['message' => 'Changes saved!'];
            }
        }

        return ['message' => 'Changes not saved! No data!'];
    }

    /**
     * @return array|Request
     */
    public
    function actionGet(): array|Request
    {
        $params = Yii::$app->request->get();

        if (!empty($params)) {
            if (isset($params['status']) && !isset($params['created_at'])) {
                $model = Request::find()
                    ->where(['status' => $params['status']])
                    ->all();
            }

            if (!isset($params['status']) && isset($params['created_at'])) {
                $model = Request::find()
                    ->where(['created_at' => $params['created_at']])
                    ->all();
            }

            if (isset($params['status']) && isset($params['created_at'])) {
                $model = Request::find()
                    ->where(['status' => $params['status']])
                    ->andWhere(['like', 'created_at', $params['created_at']])
                    ->all();
            }
        } else {
            $model = Request::find()
                ->orderBy('status')
                ->all();
        }

        if (!$model) {
            return ['message' => 'Requests not found!'];
        }

        return $model;
    }

    /**
     * @param $model Request
     * @return void
     */
    private static function SendEmail(Request $model): void
    {
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($model->email)
            ->setSubject('Response to your request № ' . $model->id)
            ->setTextBody($model->comment)
            ->send();
    }
}
