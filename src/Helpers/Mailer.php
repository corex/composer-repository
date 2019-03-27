<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Exceptions\MailerException;

class Mailer
{
    private $from;
    private $to = [];
    private $subject = null;
    private $body = '';
    private $variables = [];
    private $isHtml = false;

    /**
     * Mailer.
     *
     * @throws MailerException
     */
    public function __construct()
    {
        $config = Config::load();
        $this->from = $config->getEmailFrom();
        if ($this->from === null) {
            throw new MailerException('No from address specified.');
        }
        $this->to = $config->getEmailTos();
    }

    /**
     * Subject.
     *
     * @param string $subject
     * @return $this
     */
    public function subject(string $subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Text.
     *
     * @param string $text
     * @param bool $linebreak
     * @return $this
     */
    public function text(string $text, bool $linebreak = true)
    {
        $this->body .= $text;
        if ($linebreak) {
            $this->br();
        }
        return $this;
    }

    /**
     * Br.
     *
     * @return $this
     */
    public function br()
    {
        $this->body .= "\n";
        return $this;
    }

    /**
     * Body.
     *
     * @param string $body
     * @return $this
     */
    public function body(string $body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Variable.
     *
     * @param string $name
     * @param string $value
     */
    public function variable(string $name, string $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Variables.
     *
     * @param array $nameValues
     */
    public function variables(array $nameValues)
    {
        foreach ($nameValues as $name => $value) {
            $this->variable($name, (string)$value);
        }
    }

    /**
     * Set html.
     */
    public function setHtml()
    {
        $this->isHtml = true;
    }

    /**
     * Send.
     *
     * @throws MailerException
     */
    public function send()
    {
        if (count($this->to) == 0) {
            throw new MailerException('No emails specified.');
        }
        if ($this->subject === null) {
            throw new MailerException('Subject not specified.');
        }

        $body = $this->body;
        if (count($this->variables) > 0) {
            foreach ($this->variables as $name => $value) {
                $body = str_replace('{{' . $name . '}}', (string)$value, $body);
            }
        }

        $message = new \Swift_Message($this->subject);
        if ($this->isHtml) {
            $message->setContentType("text/html");
            $body = str_replace("\n", '<br>', $body);
        }
        $message->setFrom($this->from);
        $message->setTo($this->to);
        $message->setBody($body);

        $transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
        $mailer = new \Swift_Mailer($transport);
        $mailer->send($message);
    }
}