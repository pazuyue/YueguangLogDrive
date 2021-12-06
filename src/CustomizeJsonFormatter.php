<?php


namespace Aex\YueguangLogDrive;

use Monolog\Formatter\JsonFormatter;

class CustomizeJsonFormatter extends JsonFormatter
{
    static protected $spanId = '';
    static protected $traceId = '';
    // 重构
    public function format(array $record): string
    {
        $newRecord = [
            'level_name'=>$record['level_name'],
            'datetime' => $record['datetime']->format('Y-m-d\TH:i:s.vP'),
            'message' => $record['message'],
        ];

        if (!empty($record['context'])) {
            $newRecord['context'] = $record['context'];
        }

        /**/
        $request = app('request');
        if(is_object($request)) {
            $traceId = $request->header('requestId', '');
            if(!empty($traceId)) {
                self::$traceId = $traceId;
            }
            $newRecord['ip'] = $request->ips();
            $newRecord['path'] = $request->path();
        }

        if(empty(self::$traceId)) {
            self::$traceId = $this->genUniqId();
        }

        if(empty(self::$spanId)) {
            self::$spanId = $this->genUniqId();
        }

        $newRecord['file'] = isset($record['extra']['file'])?$record['extra']['file']:'';
        $newRecord['pid'] = getmypid();
        $newRecord['traceId'] = self::$traceId;
        $newRecord['spanId'] = self::$spanId;


        $json = $this->toJson($this->normalize($newRecord), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }

    public function genUniqId() {

        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil(8));
            return substr(bin2hex($bytes), 0, 16);

        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil(8));
            return substr(bin2hex($bytes), 0, 16);

        } else {
            $pre = rand(1, 4095);
            return uniqid(sprintf("%03x", $pre));
        }
    }
}
