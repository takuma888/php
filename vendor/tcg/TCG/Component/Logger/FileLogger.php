<?php
namespace TCG\Component\Logger;

use Psr\Log\AbstractLogger;

class FileLogger extends AbstractLogger
{


    protected $rootPath = '';

    protected $channel = '';

    protected $realPath = '';


    public function __construct($path, $channel)
    {
        $this->rootPath = $path;
        $this->channel = $channel;
        $this->init();
    }


    protected function init()
    {
        $path = $this->rootPath;
        if (!file_exists($path)) {
            $mkdir = @mkdir($path, 0775, true);
            if (!$mkdir) {
                throw new \InvalidArgumentException("创建日志根目录{$path}失败，可能没有权限，清检查");
            }
        }
        if (!is_writable($path)) {
            throw new \InvalidArgumentException("日志根目录{$path}需要有写入的权限");
        }
        $this->realPath = $path;
    }



    public function log($level, $message, array $context = array())
    {
        $dir = $this->realPath . DIRECTORY_SEPARATOR . $this->channel . DIRECTORY_SEPARATOR . $level;
        if (!file_exists($dir)) {
            $mkdir = @mkdir($dir, 0775, true);
            if (!$mkdir) {
                throw new \InvalidArgumentException("创建日志目录{$dir}失败，可能没有权限，清检查");
            }
        }
        $path = $dir . DIRECTORY_SEPARATOR . $this->channel . '_' . $level . '_' . str_replace('-', '', date('Y-m-d', time())) . '.log';
        if (!file_exists($path)) {
            $touch = touch($path);
            if (!$touch) {
                throw new \InvalidArgumentException("创建日志文件{$path}失败，可能没有权限，清检查");
            }
        }
        if (!is_writable($path)) {
            $chmod = @chmod($path, 0775);
            if (!$chmod) {
                throw new \InvalidArgumentException("日志目录{$path}需要有写入的权限");
            }
        }
        $contents = array();
        $contents[] = "[" . date('Y-m-d H:i:s', time()) . "]";
        $contents[] = $message;
        if (!empty($context)) {
            foreach ($context as $k => $v) {
                $contents[] = "{$k}#{$v}";
            }
        }
        $content = implode("\t", $contents) . "\n";

        file_put_contents($path, $content, FILE_APPEND);
    }
}