<?php
    class curl{
        private $CH;
        private $METHOD;
        private $DATA = array();
        private $HEADERS = array();
        private $REDIRECTION;
        
        public function __construct($method,$url,$data=array(),$headers=array(),$redirection=FALSE){
            $this->CH = curl_init();
            
            if($method == 'GET' && !empty($data))
                $url .= '?'.http_build_query($data);
            
            curl_setopt($this->CH, CURLOPT_URL, $url);
            curl_setopt($this->CH, CURLOPT_RETURNTRANSFER, 1);
            
            if($redirection){
				curl_setopt($this->CH, CURLOPT_FOLLOWLOCATION, true);	
			}
            
            $this->METHOD = $method;
            $this->DATA = $data;
            $this->HEADERS = $headers;
            $this->REDIRECTION = $redirection;
        }
        
        private function setHeader(){
            curl_setopt($this->CH, CURLOPT_HTTPHEADER, $this->HEADERS);
        }
        
        private function setCurlMethod(){
            if($this->METHOD == 'POST')
                curl_setopt($this->CH, CURLOPT_POST, 1);
            else if($this->METHOD == 'PUT')
                curl_setopt($this->CH, CURLOPT_PUT, 1);
            else if($this->METHOD == 'DELETE')
                curl_setopt($this->CH, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        
        private function setPostFields(){
            curl_setopt($this->CH, CURLOPT_POSTFIELDS, http_build_query($this->DATA));
        }
        
        private function getHTTPCode(){
            return curl_getinfo($this->CH, CURLINFO_HTTP_CODE);    
        }
        
        private function getError($result){
            $curlError = array();
            
            if($result === false){
                $curlError = array('ERROR' => array('message' => 'Curl error: '.curl_error($this->CH)));
            }
                                                    
            return $curlError;
        }
        
        public function execute(){
            $this->setHeader();
            $this->setCurlMethod();
            $this->setPostFields();
            
            $result = curl_exec($this->CH);
            
            $curlResponse = array();
            $curlResponse['status_code'] = $this->getHTTPCode();
            $curlResponse['error'] = $this->getError($result);
            
            if(empty($curlResponse['error']))
                $curlResponse['result'] = $result;
            
            curl_close($this->CH);
            
            return $curlResponse;
        }
    }
?>