<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\RequestSupport;
use api\modules\v1\models\RequestUser;
use common\models\Request;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
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
            'rateLimiter' => [
                'class' => RateLimiter::class,
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
            return $this->success('Request created successfully!');
        }

        return $this->error('Request not created!');
    }

    /**
     * @return string[]|array
     * @throws InvalidConfigException
     */
    public function actionPut(): array|string
    {
        $params = Yii::$app->request->getBodyParams();

        if (empty($params)) {
            return $this->error('Changes not saved! No data!');
        }

        if (!isset($params['id'])) {
            return $this->error('Request number not provided!');
        }

        if (!isset($params['comment'])) {
            return $this->error('Add a comment!');
        }

        $model = RequestSupport::findOne($params['id']);

        if (!$model) {
            return $this->error('Request not found!');
        }

        $model->comment = $params['comment'];
        $model->status = 'Resolved';

        if (!$model->save()) {
            return $this->error('Request not saved!');
        }

        self::SendEmail($model);

        return $this->success('Changes saved!');
    }

    /**
     * @return array|Request
     */
    public function actionGet(): array|Request
    {
        $params = Yii::$app->request->get();

        if (empty($params)) {
            $model = Request::find()
                ->orderBy('status')
                ->all();
        }

        if (isset($params['status']) && !isset($params['created_at'])) {
            $model = Request::find()
                ->where(['status' => $params['status']])
                ->all();
        }

        if (!isset($params['status']) && isset($params['created_at'])) {
            $model = Request::find()
                ->where(['like', 'created_at', $params['created_at']])
                ->all();
        }

        if (isset($params['status']) && isset($params['created_at'])) {
            $model = Request::find()
                ->where(['status' => $params['status']])
                ->andWhere(['like', 'created_at', $params['created_at']])
                ->all();
        }

        if (!$model) {
            return $this->error('Requests not found!');
        }

        return $model;
    }

    /**
     * @param Request $model
     * @return void
     */
    private
    static function SendEmail(Request $model): void
    {
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($model->email)
            ->setSubject('Response to your request #' . $model->id)
            ->setTextBody($model->comment)
            ->send();
    }

    /**
     * @param $msg
     * @return array
     */
    public
    function error($msg): array
    {
        return [
            'result' => false,
            'message' => $msg,
        ];
    }

    /**
     * @param $msg
     * @return array
     */
    public
    function success($msg): array
    {
        return [
            'result' => true,
            'message' => $msg,
        ];
    }
}
