<?php
/**
 * 月光
 * 自定义Log 类
 * User: Administrator
 * Date: 2021/9/24
 * Time: 11:27
 */

namespace Aex\YueguangLogDrive;

use Monolog\DateTimeImmutable;
use Monolog\Logger;
use Throwable;

class CustomizeLogger extends Logger
{
    /**
     * Adds a log record.
     *
     * @param  int     $level   The logging level
     * @param  string  $message The log message
     * @param  mixed[] $context The log context
     * @return bool    Whether the record has been processed
     *
     * @phpstan-param Level $level
     */
    public function addRecord(int $level, string $message, array $context = []): bool
    {
        $offset = 0;
        $record = null;

        foreach ($this->handlers as $handler) {
            if (null === $record) {
                // skip creating the record as long as no handler is going to handle it
                if (!$handler->isHandling(['level' => $level])) {
                    continue;
                }

                $levelName = static::getLevelName($level);


                $record = [
                    'message' => $message,
                    'context' => $context,
                    'level' => $level,
                    'level_name' => $levelName,
                    'channel' => $this->name,
                    'datetime' => new DateTimeImmutable($this->microsecondTimestamps, $this->timezone),
                    'extra' => [],
                ];

                try {
                    foreach ($this->processors as $processor) {
                        $record = $processor($record);
                    }
                } catch (Throwable $e) {
                    $this->handleException($e, $record);

                    return true;
                }
            }

            //匹配调用类名字
            if(strpos($record['extra']['file'],'\\') !== false){
                if(strpos($record['extra']['file'],'->') !== false){
                    $file_name =explode('\\',explode('->',$record['extra']['file'])[0]);
                }elseif (strpos($record['extra']['file'],':') !== false){
                    $file_name =explode('\\',explode(':',$record['extra']['file'])[0]);
                }else{
                    $file_name =explode('\\',$record['extra']['file']);
                }
            }
            elseif(strpos($record['extra']['file'],'/') !== false){
                if(strpos($record['extra']['file'],'->') !== false){
                    $file_name =explode('/',explode('->',$record['extra']['file'])[0]);
                }elseif (strpos($record['extra']['file'],':') !== false){
                    $file_name =explode('/',explode(':',$record['extra']['file'])[0]);
                }else{
                    $file_name =explode('/',$record['extra']['file']);
                }
            }else{
                $file_name[0] = 'FileNameError'; //兜底策略
            }

            $file_name = end($file_name);

            $file_name = str_replace('.php','',$file_name);

            $record['file_name'] = $file_name;
            $handler->setFormatter(new CustomizeJsonFormatter());
            // once the record exists, send it to all handlers as long as the bubbling chain is not interrupted
            try {
                if (true === $handler->handle($record)) {
                    break;
                }
            } catch (Throwable $e) {
                $this->handleException($e, $record);
                return true;
            }
        }

        return null !== $record;
    }

}
