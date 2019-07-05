<?php
    namespace HTTP;

    class Header {

        private $version = '1.1';
        private $headers = array(
            'content-type'  =>  'text/html',
            'pragma'        =>  'no-cache',
            'cache-control' =>  'no-store, no-cache, must-revalidate, post-check=0, pre-check=0'
        );


        public function getHttpVersion() {
            return $this->version;
        }

        public function SetHeader($key = false, $value = false) {
            if(!$key || !$value) return false;

            $key = strtolower($key);
            $this->headers[$key] = $value;

            return true;
        }

        public function SendHeaders() {
            if (headers_sent()) {
                die('Headers already sent.');
                return false;
            }

            foreach($this->headers as $header => $value)
                header($header . ": " . $value);
        }

        public function SetResponseCode($code = 404) {
            http_response_code($code);
        }

        public function GetResponseCode() {
            return http_response_code();
        }

    }
