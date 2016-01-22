<?php

namespace vitalosh\swiftmailer;

use Yii;
use yii\swiftmailer\Mailer as BaseMailer;

class Mailer extends BaseMailer
{
    public $useSpool = false;

    private $_spool;
    private $_spoolPath;
    private $_spoolTimeLimit;
    private $_spoolRetryLimit = 10;
    private $_spoolMessageLimit;

    public function getSpoolPath()
    {
        if ($this->_spoolPath === null)
        {
            $this->setSpoolPath('@common/runtime/mail_queue');
        }

        return $this->_spoolPath;
    }

    public function setSpoolPath($value)
    {
        $this->_spoolPath = Yii::getAlias($value);
    }

    public function setSpoolTimeLimit($value)
    {
        $this->_spoolTimeLimit = (int) $value;
    }

    public function setSpoolRetryLimit($value)
    {
        $this->_spoolRetryLimit = (int) $value;
    }

    public function setSpoolMessageLimit($value)
    {
        $this->_spoolMessageLimit = (int) $value;
    }

    protected function getSpool()
    {
        if (($this->_spool instanceof Swift_FileSpool) === false)
        {
            $this->_spool = Yii::createObject('Swift_FileSpool', [$this->spoolPath]);

            if ($this->_spoolTimeLimit)
            {
                $this->_spool->setTimeLimit($this->_spoolTimeLimit);
            }

            if ($this->_spoolRetryLimit)
            {
                $this->_spool->setRetryLimit($this->_spoolRetryLimit);
            }

            if ($this->_spoolMessageLimit)
            {
                $this->_spool->setMessageLimit($this->_spoolMessageLimit);
            }
        }

        return $this->_spool;
    }

    public function sendQueue()
    {
        if ($this->useSpool === true)
        {
            $this->spool->recover();
            return $this->spool->flushQueue($this->transport);
        }

        return 0;
    }

    protected function sendMessage($message)
    {
        if ($this->useSpool === true)
        {
            return $this->spool->queueMessage($message->getSwiftMessage());
        }
        else
        {
            return parent::sendMessage($message);
        }
    }
}
