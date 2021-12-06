<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/9/26
 * Time: 11:38
 */

namespace Aex\YueguangLogDrive;

use Illuminate\Log\ParsesLogConfiguration;
use InvalidArgumentException;
use Monolog\Handler\HandlerInterface;

class MonologCustomizeFormatter
{
    use ParsesLogConfiguration;

    /**
     * 创建一个自定义 Monolog 实例。
     *
     * @param  array $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $processors = [
            new IntrospectionProcessor()
        ];
        return new CustomizeLogger($config['driver'], [
            $this->prepareHandler(new CustomizeRotatingFileHandler(
                $config['path'] ?? '',
                $this->level($config),
                $config['bubble'] ?? true,
                $config['permission'] ?? null,
                $config['locking'] ?? false
            ), $config),
        ],$processors);
    }

    /**
     * Prepare the handler for usage by Monolog.
     *
     * @param  \Monolog\Handler\HandlerInterface  $handler
     * @param  array  $config
     * @return \Monolog\Handler\HandlerInterface
     */
    protected function prepareHandler(HandlerInterface $handler, array $config = [])
    {
        return $handler;
    }

    /**
     * Parse the string level into a Monolog constant.
     *
     * @param  array  $config
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    protected function level(array $config)
    {
        $level = $config['level'] ?? 'debug';

        if (isset($this->levels[$level])) {
            return $this->levels[$level];
        }

        throw new InvalidArgumentException('Invalid log level.');
    }

    /**
     * Get fallback log channel name.
     *
     * @return string
     */
    protected function getFallbackChannelName()
    {
        // TODO: Implement getFallbackChannelName() method.
    }

}
