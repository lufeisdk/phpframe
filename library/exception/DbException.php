<?php

namespace phpframe\exception;

use phpframe\Exception;

/**
 * Database相关异常处理类
 */
class DbException extends Exception
{
    /**
     * DbException constructor.
     * @access public
     * @param  string $message
     * @param  array $config
     * @param  string $sql
     * @param  int $code
     */
    public function __construct($message, array $config = [], $sql = '', $code = 10500)
    {
        $this->message = $message;
        $this->code = $code;

        $this->setData('Database Status', [
            'Error Code' => $code,
            'Error Message' => $message,
            'Error SQL' => $sql,
        ]);

        unset($config['user'], $config['passwd']);
        $this->setData('Database Config', $config);
    }
}