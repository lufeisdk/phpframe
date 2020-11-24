<?php

namespace phpframe\exception;

use Exception;
use phpframe\Config;
use phpframe\Log;

class Handle
{
    protected $render;
    protected $ignoreReport = [
        //'\\phpframe\\exception\\HttpException',
    ];
    protected $severity = [
        1 => 'E_ERROR',
        2 => 'E_WARNING',
        4 => 'E_PARSE',
        8 => 'E_NOTICE'
    ];

    /**
     * Report or log an exception.
     *
     * @access public
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (!$this->isIgnoreReport($exception)) {
            // 收集异常数据
            if (Config::get('app_debug', 'app')) {
                $data = [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'message' => $this->getMessage($exception),
                    'code' => $this->getCode($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";
            } else {
                $data = [
                    'code' => $this->getCode($exception),
                    'message' => $this->getMessage($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}";
            }

            if ($exception instanceof \phpframe\Exception) {
                $data = $exception->getData();
                if (!empty($data)) {
                    $str = PHP_EOL;
                    foreach ($data as $label => $items) {
                        $item = json_encode($items, JSON_UNESCAPED_UNICODE);
                        $str .= "[{$label}]{$item}" . PHP_EOL;
                    }
                    $log .= $str;
                }
            }

            if (Config::get('log_trace', 'app')) {
                $log .= "\r\n" . $exception->getTraceAsString();
            }

            Log::getInstance()->setLogFile('debug.log')
                ->write($log);
        }
    }

    protected function isIgnoreReport(Exception $exception)
    {
        foreach ($this->ignoreReport as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取错误编码
     * ErrorException则使用错误级别作为错误编码
     * @access protected
     * @param  \Exception $exception
     * @return integer                错误编码
     */
    protected function getCode(Exception $exception)
    {
        $code = $exception->getCode();

        if (!$code && $exception instanceof ErrorException) {
            $code = $exception->getSeverity();
        }

        return $code ? ($this->severity[$code] ?? 'E_ERROR') : 'E_ERROR';
    }

    /**
     * 获取错误信息
     * ErrorException则使用错误级别作为错误编码
     * @access protected
     * @param  \Exception $exception
     * @return string                错误信息
     */
    protected function getMessage(Exception $exception)
    {
        $message = $exception->getMessage();

        return $message;
    }
}
