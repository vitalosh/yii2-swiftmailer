<?php

namespace vitalosh\swiftmailer\controllers;

use Yii;
use yii\console\Controller;

class MailerController extends Controller
{
    public function actionSend()
    {
        $count = Yii::$app->mailer->sendQueue();
        echo "Sent " . $count . " letters.";
    }
}
