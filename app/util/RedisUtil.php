<?php


namespace app\util;


class RedisUtil
{
    /**
     * 获取Redis连接
     */
    protected static function getRedisConnection(): \Redis
    {
        $redis = new \Redis();
        $redisConf = config('redis');
        $redis->connect($redisConf['host'], $redisConf['port']);
        if ($redisConf['user'] && $redisConf['pass']){
            $redis->auth($redisConf['user'].':'.$redisConf['pass']);
        }
        //echo "Redis Server is running: " . $redis->ping() . "\r\n";
        return $redis;
    }

    /**
     * 逐条lPush
     * @param $queue
     * @param $value
     * @return false|int
     */
    public static function lPush($queue,$value){
        if (is_array($value) || is_object($value)){
            $value = json_encode($value,JSON_UNESCAPED_UNICODE);
        }
        $redis = self::getRedisConnection();
        return $redis->lPush($queue,$value);
    }

    /**
     * 批量lPush
     * @param $queue
     * @param $values
     * @return false|mixed
     */
    public static function groupLPush($queue,$values): bool
    {
        $redis = self::getRedisConnection();
        $toPushQueue = [];
        $toPushQueue[] = $queue;
        foreach ($values as $value){
            if (is_array($value) || is_object($value)){
                $value = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
            $toPushQueue[] = $value;
        }
        return call_user_func_array([$redis,'lPush'],$toPushQueue);
    }

    /**
     * 取队列长度
     * @param $queue
     * @return bool|int
     */
    public static function lLength($queue){
        return self::getRedisConnection()->lLen($queue);
    }

    /**
     * 取队尾元素
     * @param $queue
     * @return bool|int
     */
    public static function rPop($queue): bool
    {
        return self::getRedisConnection()->rPop($queue);
    }


    public static function set($key,$value,$ex = 0): bool
    {
        if (!$ex){
            return self::getRedisConnection()->set($key,$value);
        }
        return self::getRedisConnection()->setex($key,$ex,$value);
    }

    /**
     * 删除一个或多个键
     * @param $key
     * @return bool
     */
    public static function del($key): bool
    {
        $redis = self::getRedisConnection();
        return call_user_func_array([$redis,'del'],$key);
    }
}