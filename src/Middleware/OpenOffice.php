<?
    namespace Middleware;

    include_once(__DIR__ . "/../Config/Service.php");

    class OpenOffice {

        public $debug = false;
        public $proxy_host;
        public $proxy_port;

        public $remoteEndPoint;
        public $remoteEndPointBackup;

        public $source_format;
        public $target_format;
        public $source_file;
        public $raw_file;

        public $accessKey;
        public $secretKey;

        public $accessKeyBackup;
        public $secretKeyBackup;

        public $inDir;
        public $outDir;


        /* SERVICE VARIABLES */

        public $upload_name_source = false; // For Upload [Source Name]
        public $upload_name_target = false; // For Upload [Target Name]


        public function __construct() {
            $this->LoadConfiguration();
        }

        private function LoadConfiguration() {
            global $OOO_CONVERT_VALIDEXTENSIONS;

            $this->debug=OOO_CONVERT_DEBUG;
            $this->proxy_host=OOO_CONVERT_PROXY_HOST;
            $this->proxy_port=OOO_CONVERT_PROXY_PORT;
            $this->remoteEndPoint=OOO_CONVERT_REMOTEENDPOINT;
            $this->accessKey=OOO_CONVERT_ACCESSKEY;
            $this->secretKey=OOO_CONVERT_SECRETKEY;

            // SERVER
            $this->inDir = OOO_CONVERT_INDIR;
            $this->outDir = OOO_CONVERT_OUTDIR;
            $this->execPath = OOO_CONVERT_EXECPATH;
            $this->jodPath = OOO_CONVERT_JODPATH;
            $this->validExtensions = $OOO_CONVERT_VALIDEXTENSIONS;

            // $this->remoteEndPointBackup=OOO_CONVERT_REMOTEENDPOINT_BACKUP;
            // $this->accessKeyBackup=OOO_CONVERT_ACCESSKEY_BACKUP;
            // $this->secretKeyBackup=OOO_CONVERT_SECRETKEY_BACKUP;
        }

        public function SetSourceFormat($SourceFormat = 'odt') {
            $this->source_format = $SourceFormat;

            return $this->source_format;
        }

        public function SetTargetFormat($TargetFormat = 'pdf') {
            $this->target_format = $TargetFormat;

            return $this->target_format;
        }

        public function SetSourceFile($source_file = false) {
            if(!$source_file || !file_exists($source_file)) return false;

            $this->source_file = $source_file;
        }

        private function ServiceExecute($cmd = false, &$results = array()) {
            if(!$cmd) return false;

            $results = array();
            @exec($cmd, $results);

            if(!count($results)) return false;

            return $results;
        }

        public function ServiceUp() {
            $cmd     = "netstat -apn | grep 8100";
            $results = array();
            $this->ServiceExecute($cmd, $results);

            if (!count($results)) return false;

            return true;
        }

        public function ServiceUpload($remote_connection = false) {
            if(!$this->upload_name_source || !$this->raw_file) return false;

            $file_properties = pathinfo($this->raw_file);
            $file_extension  = $file_properties['extension'];

            if(!in_array($file_properties['extension'], $this->validExtensions)) {
                // writeLog("File is invalid");
                return false;
            }


            if(!file_exists($this->outDir)) mkdir($this->outDir, 0755, true);
            if(!file_exists($this->inDir)) mkdir($this->inDir, 0755, true);

            $this->upload_name_target = $this->inDir . "/" . date('ymdhis') . uniqid() . ".{$file_extension}";


            $result = false;
            if($remote_connection)
                $result = move_uploaded_file($this->upload_name_source, $this->upload_name_target);
            else
                $result = copy(ROOT . $this->raw_file, $this->upload_name_target);

            if(!$result) return false;

            return basename($this->upload_name_target);
        }

        public function ServiceConvert() {
            if(!$this->upload_name_source || !$this->upload_name_target) return false;

            if(!file_exists($this->inDir)) mkdir($this->inDir, 0755, true);

            if (file_put_contents($this->upload_name_target, "") === FALSE)
                return false;


    		$cmd = $this->execPath." ".$this->jodPath." ".escapeshellarg($this->upload_name_source)." ".escapeshellarg($this->upload_name_target);
    		$results = array();
    		$this->ServiceExecute($cmd, $results);

    		if (!filesize($this->upload_name_target))
    			return false;

            return true;
        }

        public function ServiceGet() {
            if(!$this->upload_name_target) return false;
            if(!is_readable($this->upload_name_target)) return false;

            return file_get_contents($this->upload_name_target);
        }

        public function ServiceLaunch() {
            if($this->ServiceUp()) return true;

            return false; // For now, should remain offline.

            $fp = fopen(OOOCONV_ROOT . "tmp/kill-office-lock.txt", "r+");

    		// acquire an exclusive lock
    		if (flock($fp, LOCK_EX))
    		{
    			// truncate file
    			ftruncate($fp, 0);
    			fwrite($fp, "Write something here\n");
    			// flush output before releasing the lock
    			fflush($fp);
    		}
    		else return false;

            print_r(OOOCONV_ROOT . "tmp/kill-office-lock.txt");
            die;

            $cmd = OOO_CONVERT_SOFFICE_PATH . ' -headless -accept="socket,host=127.0.0.1,port=8100;urp;" -nofirststartwizard -nologo -norestore & > /dev/null 2>&1';
            $this->ServiceExecute($cmd, $results);

            return true;
        }
    }
?>
