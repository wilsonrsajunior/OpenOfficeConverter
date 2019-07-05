<?php
    namespace Client;

    use Middleware\OpenOffice as OpenOffice;
    use HTTP\Handler as Connection;

    class Client extends OpenOffice {
        private $active_connection = false;
        private $remote_pdf_resource = false;

        public function Launch_ServiceStart() {
            $this->active_connection = new Connection();

            $this->active_connection->SecureSetVal('remote_target', $this->remoteEndPoint);
            $this->active_connection->SecureSetVal('public_key', $this->accessKey);
            $this->active_connection->SecureSetVal('private_key', $this->secretKey);
            $this->active_connection->SecureSetVal('request_type', 'POST');
            $this->active_connection->SecureSetVal('download_header', true);
            $this->active_connection->SecureSetVal('request_content', array("action" => "up"));

            $server_status = $this->active_connection->Connect();
            if(!$server_status || $server_status != 200) return false;

            return true;
        }

        public function Launch_ServiceUpload() {
            // should be used also in the validation below at some point -- $this->active_connection
            if(!$this->source_file) return false;

            $this->active_connection = new Connection();

            $content_file = $this->active_connection->AttachFile($this->source_file);
            if(!$content_file) return false;

            $this->active_connection->SecureSetVal('remote_target', $this->remoteEndPoint);
            $this->active_connection->SecureSetVal('public_key', $this->accessKey);
            $this->active_connection->SecureSetVal('private_key', $this->secretKey);
            $this->active_connection->SecureSetVal('request_type', 'POST');
            $this->active_connection->SecureSetVal('download_header', true);
            $this->active_connection->SecureSetVal('request_content', array("fileIn" => $content_file, "action" => "upload", "from" => $this->source_format, "to" => $this->target_format));

            $server_status = $this->active_connection->Connect();

            if(!$server_status || $server_status != 201) return false;


            $this->remote_pdf_resource = $this->active_connection->GetHeader('x-ooo-resourceid');

            return $this->remote_pdf_resource;
        }

        public function Launch_ServiceConvert() {
            // should be used also in the validation below at some point -- $this->active_connection
            if(!$this->source_file) return false;

            $this->active_connection = new Connection();

            $this->active_connection->SecureSetVal('remote_target', $this->remoteEndPoint);
            $this->active_connection->SecureSetVal('public_key', $this->accessKey);
            $this->active_connection->SecureSetVal('private_key', $this->secretKey);
            $this->active_connection->SecureSetVal('request_type', 'POST');
            $this->active_connection->SecureSetVal('download_header', true);
            $this->active_connection->SecureSetVal('request_content',
                array(
                    "action" => "convert",
                    "resourceId" => $this->remote_pdf_resource,
                    "from" => $this->source_format,
                    "to" => $this->target_format,
                    "force_debug" => true
                )
            );

            $server_status = $this->active_connection->Connect();

            if(!$server_status || $server_status != 200) return false;

            return true;
        }

       /**
        * gets the converted file from the server and saves it to a predefined directory
        *
        * @param    string  resourceId return by a previous call to upload method
        * @param    boolean  return document as a file or as a data string
        * @return	string	file content on success or false on failure
        * @access   public
        */

        public function Launch_ServiceTransferDocument() {
            // should be used also in the validation below at some point -- $this->active_connection
            if(!$this->source_file) return false;

            $this->active_connection = new Connection();

            $this->active_connection->SecureSetVal('remote_target', $this->remoteEndPoint);
            $this->active_connection->SecureSetVal('public_key', $this->accessKey);
            $this->active_connection->SecureSetVal('private_key', $this->secretKey);
            $this->active_connection->SecureSetVal('request_type', 'POST');
            $this->active_connection->SecureSetVal('request_content',
                array(
                    "action" => "get",
                    "resourceId" => $this->remote_pdf_resource,
                    "from" => $this->source_format,
                    "to" => $this->target_format,
                    "force_debug" => true
                )
            );

            $server_status = $this->active_connection->Connect();

            if(!$server_status || $server_status != 200) return false;

            return $this->active_connection->GetResultContent();
        }

    }
?>
