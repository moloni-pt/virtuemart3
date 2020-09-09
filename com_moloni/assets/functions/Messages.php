<?php

namespace Moloni\Functions;

/**
 * Class Messages
 * @package Moloni\Functions
 */
class Messages
{
    public static $messages = [];

    /**
     * Adiciona uma mensagem e guarda num array de sessão
     *
     * @param string $msg Mensagem a ser adiciona a sessão
     *
     * @return null
     */
    public static function addSessionMessage($msg = "")
    {
        if (!is_array($_SESSION['messages'])) {
            $_SESSION['messages'] = [];
        }

        $_SESSION['messages'][] = $msg;

        return null;
    }

    /**
     * Retorna um array de mensagens guardadas na sessão
     *
     * @return array
     */
    public static function getSessionMessages()
    {
        if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])) {
            $messages = $_SESSION['messages'];
        } else {
            $messages = [];
        }

        unset($_SESSION['messages']);
        return $messages;
    }

    /**
     * Faz print das mensagens
     *
     * @param string $format Formato da msg
     *
     * @return bool
     */
    public static function printMessages($format = 'html')
    {
        self::checkSessionMessages();

        if (!empty(self::$messages) && is_array(self::$messages)) {
            foreach (self::$messages as $message) {
                if ($format === 'html') {
                    echo $message;
                }
            }
        }

        return true;
    }

    /**
     * Adiciona as mensagens guardadas na sessão ao messages[]
     *
     * @return bool
     */
    private static function checkSessionMessages()
    {

        $sessionMessages = self::getSessionMessages();
        if (!empty($sessionMessages) && is_array($sessionMessages)) {
            foreach ($sessionMessages as $sessionMessage) {
                self::$messages[] = $sessionMessage;
            }
        }

        return true;
    }
}
