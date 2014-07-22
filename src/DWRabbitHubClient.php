<?php
    require_once('curl.php');

    class DWRabbitHubClient {
        private $EXCEPTION_CODE = array('INVALID_URL' => 100, 'EMPTY_STRING' => 101);
        private $TYPE;
        private $RABBITHUB_URL;
        private $NAME;
        private $PAYLOAD = array();
        
        //$name is == to exchange or queue name
        /**
         *
         */
        public function __construct($type='exchange',$name,$uri_scheme = 'http',$username,$password,$host,$port,$rabbit_mq_vhost='') {
            $this->TYPE = $type;
            
            if($username == '' || $password == '' || $host == '' || $port == '')
                throw new Exception('RabbitHub API URL is invalid',$this->EXCEPTION_CODE['INVALID_URL']);
            else
                $this->RABBITHUB_URL = $uri_scheme.'://'.$username.':'.$password.'@'.$host.':'.$port.'/'.$rabbit_mq_vhost;
            
            if(empty($name))
                throw new Exception('Exchange or Queue Name should not be empty',$this->EXCEPTION_CODE['EMPTY_STRING']);
            else
                $this->NAME = $name;
        }
        
        public function setSubscribeParams($mode,$callback,$topic,$verify,$lease,$token='') {
            try {
                $this->setHubMode($mode);
                $this->setHubCallback($callback);
                $this->setHubTopic($topic);
                $this->setHubVerify($verify);
                $this->setHubLease($lease);
                $this->setToken($token);
            } catch (Exception $e) {
                echo $e->getCode().':'.$e->getMessage();
            }
        }
        
        public function setPublishParams($message,$topic) {
            try {
                $this->setMessage($message);
                $this->setHubTopic($topic);
            } catch (Exception $e) {
                echo $e->getCode().':'.$e->getMessage();
            }
        }
        
        public function sendRequest($request_type='subscribe',$http_method='POST') {
            $url = $this->RABBITHUB_URL;
            
            if($request_type == 'subscribe') {
                $url .= '/subscribe/'.(($this->TYPE == 'exchange') ? 'x' : 'q').'/'.$this->NAME;
            } else {
                $url .= '/endpoint/'.(($this->TYPE == 'exchange') ? 'x' : 'q').'/'.$this->NAME.'?hub.topic='.$this->PAYLOAD['hub.topic'];
                unset($this->PAYLOAD['hub.topic']);
            }
            
            $curl = new curl($http_method,$url,$this->PAYLOAD);
            return $curl->execute();
        }
        
        /*============================================================
         * Subscribe/Publish Private Methods
         *============================================================
         */
        
        /**
         *Sets the Hub.Mode to be used for the RabbitHub API request
         *@var $mode Str - subscribe or unsubscribe
         */
        private function setHubMode($mode){
            if(!empty($mode))
                $this->PAYLOAD['hub.mode'] = $mode;
            else    
                throw new Exception('Mode should not be empty',$this->EXCEPTION_CODE['EMPTY_STRING']);
        }
        
        /**
         *Sets the Hub.Callback to be used for the RabbitHub API request
         *@var $callback Str - URL format
         */
        private function setHubCallback($callback){
            if(!empty($callback) && filter_var($callback, FILTER_VALIDATE_URL))
                $this->PAYLOAD['hub.callback'] = $callback;
            else
                throw new Exception('Callback URL is empty or not a proper URL',$this->EXCEPTION_CODE['INVALID_URL']);
        }
        
        /**
         *Sets the Hub.Topic to be used for the RabbitHub API request
         *@var $topic Str
         */
        private function setHubTopic($topic){
            if(!empty($topic))
                $this->PAYLOAD['hub.topic'] = $topic;
            else
                throw new Exception('Topic should not be empty',$this->EXCEPTION_CODE['EMPTY_STRING']);
        }
        
        /**
         *Sets the Hub.Verify to be used for the RabbitHub API request. If the parameter passed is empty then it will be set to async
         *@var $verify Str
         */
        private function setHubVerify($verify){
            if(!empty($verify))
                $this->PAYLOAD['hub.verify'] = strtolower($verify);
            else
                $this->PAYLOAD['hub.verify'] = 'async';
        }
        
        /**
         *Sets the Hub.Lease_Seconds to be used for the RabbitHub API request
         *@var $lease Int - Minimum lease is 2592000 (30 days) and Maximum lease approximately 1000 years
         */
        private function setHubLease($lease){
            if(is_numeric($lease) && $lease >= 2592000)
                $this->PAYLOAD['hub.lease_seconds'] = $lease;
            else
                $this->PAYLOAD['hub.lease_seconds'] = 2592000;
        }
        
        /**
         *Sets the Token to be used for the RabbitHub API request. This is important when Unsubscribing
         *@var $token Str - Token is generated when subscription is made and RabbitHub sends the Token to the Callback URL
         */
        private function setToken($token){
            if(!empty($token))
                $this->PAYLOAD[] = $token;
        }
        
        /**
         *This function is mainly used when doing a Publish. Sets the Message that will be sent through RabbitHub
         *@var $message Str
         */
        private function setMessage($message){
            if(!empty($message))
                $this->PAYLOAD[] = $message;
            else
                throw new Exception('Message should not be empty',2);
        }
    }
?>