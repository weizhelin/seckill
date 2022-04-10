<?php


namespace app\util;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMq
{

    protected function getMqParam($type = ''): array
    {
        return config('rabbitmq');
    }

    protected function getStreamConnection(array $mq): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            $mq['host'], $mq['port'], $mq['user'], $mq['pwd'], $mq['vhost'],false,'AMQPLAIN',null,'en_US',30,30
        );
    }

    protected function getChannel(AMQPStreamConnection $connection,array $mq): \PhpAmqpLib\Channel\AMQPChannel
    {
        $channel = $connection->channel();
        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $channel->queue_declare($mq['queue'], false, true, false, false, false);
        $channel->exchange_declare($mq['exchange'], 'direct', false, true, false);
        $channel->queue_bind($mq['queue'], $mq['exchange'], $mq['routingKey']);
        return $channel;
    }
}