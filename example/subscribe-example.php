<?php
    require_once('DWRabbitHubClient.php');
    
    $subscriber = new DWRabbitHubClient($type='exchange',$typeName='my_rabbitmq_exchange',$uriScheme='http',$username='rabbitmq_user',$password='rabbitmq_password',$host='rabbit_mq_ip_or_url','rabbitmq_port',$vhost='rabbitmq_vhost');
    $subscriber->setSubscribeParams($_POST['mode'],$_POST['callback'],$_POST['topic'],$_POST['verify'],$_POST['lease']);
    echo json_encode($subscriber->sendRequest($request_type='subscribe','POST'),true);
?>