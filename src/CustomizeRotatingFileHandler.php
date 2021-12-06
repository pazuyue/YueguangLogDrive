<?php
namespace Aex\YueguangLogDrive;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/24
 * Time: 12:54
 */
use Monolog\Handler\StreamHandler;

/**
 * 自定义日志处理类
 * Class CustomizeRotatingFileHandler
 * @package App\Logging
 */
class CustomizeRotatingFileHandler extends StreamHandler
{
    private $errorMessage = null;

    /**
     * {@inheritDoc}
     */
    protected function write(array $record): void
    {
        if (!is_resource($this->stream)) {
            $url = $this->url;
            $url = $this->urlFormat($url,$record);
            if (null === $url || '' === $url) {
                throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }
            $this->createDir($url);
            $this->errorMessage = null;
            set_error_handler([$this, 'customErrorHandler']);
            $stream = fopen($url, 'a');
            if ($this->filePermission !== null) {
                @chmod($url, $this->filePermission);
            }
            restore_error_handler();
            if (!is_resource($stream)) {
                $this->stream = null;

                throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened in append mode: '.$this->errorMessage, $url));
            }
            $this->stream = $stream;
        }

        $stream = $this->stream;
        if (!is_resource($stream)) {
            throw new \LogicException('No stream was opened yet');
        }

        if ($this->useLocking) {
            // ignoring errors here, there's not much we can do about them
            flock($stream, LOCK_EX);
        }

        $this->streamWrite($stream, $record);

        if ($this->useLocking) {
            flock($stream, LOCK_UN);
        }

        $this->close();
    }

    /**
     * Write to stream
     * @param resource $stream
     * @param array    $record
     *
     * @phpstan-param FormattedRecord $record
     */
    protected function streamWrite($stream, array $record): void
    {
        fwrite($stream, (string) $record['formatted']);
    }

    private function customErrorHandler(int $code, string $msg): bool
    {
        $this->errorMessage = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $msg);

        return true;
    }

    private function getDirFromStream(string $stream): ?string
    {
        $pos = strpos($stream, '://');
        if ($pos === false) {
            return dirname($stream);
        }

        if ('file://' === substr($stream, 0, 7)) {
            return dirname(substr($stream, 7));
        }

        return null;
    }

    private function createDir(string $url): void
    {
        $dir = $this->getDirFromStream($url);
        if (null !== $dir && !is_dir($dir)) {
            $this->errorMessage = null;
            set_error_handler([$this, 'customErrorHandler']);
            $status = mkdir($dir, 0777, true);
            restore_error_handler();
            if (false === $status && !is_dir($dir)) {
                throw new \UnexpectedValueException(sprintf('There is no existing directory at "%s" and it could not be created: '.$this->errorMessage, $dir));
            }
        }
    }

    protected  function urlFormat($url,$record) :string
    {

        $urlArray = explode('/',$url);
        if (!empty($record['file_name']) && is_string($record['file_name'])){
            $urlArray[array_key_last($urlArray)] = $record['file_name'].'/'.$record['file_name'].'_'.strtolower($record['level_name']).'_'.date('Y-m-d').'_'.end($urlArray);
        }else{
            $urlArray[array_key_last($urlArray)] = strtolower($record['level_name']).'_'.date('Y-m-d').'_'.end($urlArray);
        }
        $url = implode('/', $urlArray);
        return $url;
    }


}
