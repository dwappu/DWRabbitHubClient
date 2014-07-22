<?php
    require_once('DWRabbitHubClient.php');
    
    $publisher = new DWRabbitHubClient($type='exchange',$typeName='my_rabbitmq_exchange',$uriScheme='http',$username='rabbitmq_user',$password='rabbitmq_password',$host='rabbit_mq_ip_or_url','rabbitmq_port',$vhost='rabbitmq_vhost');
    $publisher->setPublishParams($_POST['message'],$_POST['topic']);
    echo json_encode($publisher->sendRequest($request_type='publish','POST'),true);
?>