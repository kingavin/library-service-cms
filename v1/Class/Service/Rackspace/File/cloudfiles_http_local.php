<?php
require_once("cloudfiles_http.php");

class CF_Http_Local extends CF_Http
{
	public function get_object_to_stream($containerName, $objectName, &$resource, $hdrs=array())
	{
        if (!is_resource($resource)) {
            throw new SyntaxException(
                "Resource argument not a valid PHP resource.");
        }
        $conn_type = "GET_CALL";
        $url_path = $this->_make_path("STORAGE", $containerName, $objectName);
        $this->_obj_write_resource = $resource;
        $this->_write_callback_type = "OBJECT_STREAM";
        $return_code = $this->_send_request($conn_type, $url_path, $hdrs);
        if (!$return_code) {
            $this->error_str .= ": Failed to obtain valid HTTP response.";
            return array($return_code, $this->error_str);
        }
        if ($return_code == 404) {
            $this->error_str = "Object not found.";
            return array($return_code, $this->error_str);
        }
        if (($return_code < 200) || ($return_code > 299
                && $return_code != 412 && $return_code != 304)) {
            $this->error_str = "Unexpected HTTP return code: $return_code";
            return array($return_code, $this->error_str);
        }
        return array($return_code, $this->response_reason);
	}
}