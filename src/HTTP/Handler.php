<?
    namespace HTTP;

    class Handler {
        private $remote_target       = false;
        private $public_key          = false;
        private $private_key         = false;
        private $request_type        = false;
        private $request_content     = array();
        private $connection_options  = array();
        private $connection_headers  = array();

        protected $connection        = false;
        protected $result_header     = false;
        protected $result_content    = false;
        protected $result_error      = false;
        protected $result_error_code = false;

        private $download_header     = false;
        protected $beautified_header = array();

        private $connection_approved = false; // Flag to make sure we won't run a request without permission.

        public function Connect() {
            if (!$this->request_type) return false;

            $this->LoadOptions();

            switch ($this->request_type) {
                case "GET":
                    $this->Get();
                    break;
                case "POST".
                    $this->Post();
                    break;
            }

            // Connected
            return $this->result_code;
        }

        private function LoadOptions() {
            $this->connection_options = array(
                CURLOPT_RETURNTRANSFER => true,     // return web page
                CURLOPT_HEADER         => false,    // don't return headers
                // CURLOPT_HTTPHEADER     => array('Connection: Keep-Alive', 'Keep-Alive: 600'), // Keep alive for persistent connections.
                CURLOPT_FOLLOWLOCATION => false,     // follow redirects
                CURLOPT_ENCODING       => "",       // handle all encodings
                CURLOPT_USERAGENT      => "Wilson", // who am i
                CURLOPT_AUTOREFERER    => true,     // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 400,      // timeout on connect
                CURLOPT_TIMEOUT        => 120,      // timeout on response
                CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
                CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
                CURLOPT_URL            => $this->remote_target
            );
        }

        private function Get() {

            if (!empty($this->request_content))
                $this->connection_options[CURLOPT_URL] = Parser::ParseGet($this->remote_target, $this->request_content);

            if($this->download_header) $this->connection_options[CURLOPT_HEADER] = true;

            $this->SecureSetVal('connection_approved', false);
            $this->ExecuteConnection();

            return true;
        }

        private function Post() {

            if(!empty($this->connection_headers)) {
                $this->connection_options = $this->connection_options + $this->connection_headers;
                $this->connection_headers = array();
            }

            $this->connection_options[CURLOPT_POST] = true;
            $this->connection_options[CURLOPT_POSTFIELDS] = $this->request_content;
            if($this->download_header) $this->connection_options[CURLOPT_HEADER] = true;

            $this->SecureSetVal('connection_approved', true);
            $this->ExecuteConnection();
            return;
        }

        private function ExecuteConnection($debug = false) {
            if (!$this->connection_approved) return false;

            $this->connection = curl_init();
            curl_setopt_array( $this->connection, $this->connection_options );

            $this->result_content    = curl_exec( $this->connection );
            $this->result_error      = curl_error( $this->connection );
            $this->result_header     = curl_getinfo( $this->connection );
            $this->result_error_code = curl_errno( $this->connection );
            $this->result_code       = $this->result_header['http_code'];
            curl_close( $this->connection );

            $this->result_header['errno']   = $this->result_error;
            $this->result_header['errmsg']  = $this->result_code;
            $this->result_header['content'] = $this->result_content;

            if ($debug) {
                var_dump($this);
                die;
            }

            if ($this->connection_options[CURLOPT_HEADER] == true) $this->beautified_header = Parser::ParseHeader($this->result_content);

            return;
        }

        public function AttachFile($source_file) {
            $cFile = Parser::ParseCurlFile($source_file);
            if (!$cFile) return false;

            $this->connection_headers[CURLOPT_HTTPHEADER] = array("Content-MD5" => md5_file($source_file));

            return $cFile;
        }

        public function SecureSetVal($key = false, $val) {
            if (!$key) return false;

            $this->$key = $val;

            return true;
        }

        public function GetHeader($needle = false) {
            if (!$needle || empty($this->beautified_header)) return false;

            if(!isset($this->beautified_header[$needle])) return false;

            $header = $this->beautified_header[$needle];

            return $header;
        }

        public function GetResultContent() {
            if (!$this->result_content) return false;

            return $this->result_content;
        }
    }
?>
