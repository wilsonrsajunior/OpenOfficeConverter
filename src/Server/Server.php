<?
    namespace Server;

    use Middleware\OpenOffice as OpenOffice;
    use HTTP\Header as Header;

    class Server extends OpenOffice {
        private $headers;

        public function __construct() {
            $this->headers = new Header;
            parent::__construct();
            $this->headers->setHeader("Allow", "POST");
        }

        public function Up() {
            if(!$this->ServiceLaunch()) {
                $this->headers->setResponseCode(404);
                return false;
            }

            $this->headers->setResponseCode(200);
            return true;
        }

        /**
        * launches OOO in a headless mode
        *
        * @return	mixed	HTTP 200 on success or HTTP 404 on failure
        * @access   public
        */

        public function Upload() {
            if(!isset($_FILES['fileIn']) || empty($_FILES['fileIn'])) {
                // writeLog("File is missing");
    			$this->headers->SetResponseCode(404);
                return false;
            }

            $file            = $_FILES['fileIn'];
            // $file_properties = pathinfo($file['name']);
            // $file_extension  = $file_properties['extension'];
            //
            // if(!in_array($file_properties['extension'], $this->validExtensions)) {
            //     // writeLog("File is invalid");
            //     $this->headers->SetResponseCode(404);
            //     return false;
            // }

            $this->raw_file = $file;
            $this->upload_name_source = $file['tmp_name'];

            if(!$this->ServiceUpload(true)) {
                // writeLog("Error while moving the file");
                $this->headers->SetResponseCode(404);
                return false;
            }

            $this->headers->SetResponseCode("201");
            $this->headers->SetHeader("x-ooo-resourceid", basename($this->upload_name_target));
            $this->headers->SendHeaders();
    	}

        public function Convert($resourceId) {
            $this->upload_name_target = $this->outDir."/".$resourceId.".".$this->target_format;
            $this->upload_name_source = $this->inDir."/".$resourceId;

    		if (!is_readable($this->upload_name_source)) {
    			$this->headers->SetResponseCode(404);
                $this->headers->SendHeaders();
                return false;
    		}

            if(!$this->ServiceConvert()) {
                $this->headers->SetResponseCode(404);
                $this->headers->SendHeaders();
                return false;
            }

    		$this->headers->SetResponseCode(200);
            $this->headers->SendHeaders();
    	}

        public function Get($resourceId) {
            if(!$this->upload_name_target) $this->upload_name_target = $this->outDir."/".$resourceId.".".$this->target_format;

            $pdf_content = $this->ServiceGet();

            if(!$pdf_content) {
                $this->headers->SetResponseCode(404);
                $this->headers->SendHeaders();
                return false;
            }

            $this->headers->SetResponseCode(200);
            $this->headers->SetHeader("Content-Length", filesize($this->upload_name_target));
            $this->headers->SetHeader("Content-Type", "binary/octet-stream");
            $this->headers->SendHeaders();


            return $pdf_content;
    	}

        public function remove($resourceId) {
    		$fileIn  = $this->inDir."/".$resourceId;
    		$fileOut = $this->outDir."/".$resourceId.".".$this->getConvertTo();

    		if (is_readable($fileIn) || is_readable($fileOut))
    		{
    			if (@unlink($fileIn) === TRUE || @unlink($fileOut) === TRUE)
    				$this->headers->SetResponseCode(200);
    			else
    				$this->headers->SetResponseCode(404);
    		}
    		else {
    			$this->headers->SetResponseCode(404);
    		}
    	}

    }
?>
