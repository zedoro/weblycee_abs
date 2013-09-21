    <div>HELLO</div>

<?php
/*
 * Upload and download files over HTTP within PHP code
 *
 * PHP versions 4 and 5
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author 	  Nashruddin Amin <me@nashruddin.com>
 * @copyright Nashruddin Amin 2008
 * @license	  GNU General Public License 3.0
 * @version   1.1
 */
 
/**
 * get a file from specified url
 *
 * @param string $remote url of the file
 * @param string $local  save contents to this file
 *
 * @return boolen true on success, false on failure.
 */
function get_file($remote, $local)
{
	/* get hostname and path of the remote file */
	$host = parse_url($remote, PHP_URL_HOST);
	$path = parse_url($remote, PHP_URL_PATH);
	
	/* prepare request headers */
	$reqhead = "GET $path HTTP/1.1\r\n"
			 . "Host: $host\r\n"
			 . "Connection: Close\r\n\r\n";
	
	/* open socket connection to remote host on port 80 */
	$fp = fsockopen($host, 80, $errno, $errmsg, 30);
	
	/* check the connection */
	if (!$fp) {
		print "Cannot connect to $host!\n";
		return false;
	}
	
	/* send request */
	fwrite($fp, $reqhead);

	/* read response */
	$res = "";
	while(!feof($fp)) {
		$res .= fgets($fp, 4096);
	}		
	fclose($fp);
	
	/* separate header and body */
	$neck = strpos($res, "\r\n\r\n");
	$head = substr($res, 0, $neck);
	$body = substr($res, $neck+4);

	/* check HTTP status */
	$lines = explode("\r\n", $head);
	preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $lines[0], $m);
	$status = $m[2];

	if ($status == 200) {
		file_put_contents($local, $body);
		return(true);
	} else {
		return(false);
	}
}

/**
 * upload a file to server
 *
 * @param string $fname   	name of file to upload
 * @param string $handler 	server-side script to handle the uploading file
 * @param string $field		name of the form's input (<input type="file" />)
 *
 * @return boolean true on success, false on failure
 */
function send_file($fname, $handler, $field)
{
	/* check if file exists */
	if (!file_exists($fname)) {
		echo 'file not found.';
		return false;
	}

	/* get file's extension */
	preg_match("/\.([^\.]+)$/", $fname, $matches);
	$ext = $matches[1];
	
	/* guess mimetype from file's extension 
	   please add some more mimetypes here */
	switch(strtolower($ext)) {
		case "doc":
			$mime = "application/msword";
			break;
		case "jpeg":
		case "jpg":		
		case "jpe":
			$mime = "image/jpeg";
			break;
		case "gif":
			$mime = "image/gif";
			break;
		case "pdf":
			$mime = "application/pdf";
			break;
		case "png":
			$mime = "image/png";
			break;
		case "txt":
		default:
			$mime = "text/plain";
			break;
	}		
	
	/* get hostname and path of remote script */
	$host = parse_url($handler, PHP_URL_HOST);
	$path = parse_url($handler, PHP_URL_PATH);
	
	/* setup request header and body */
	$boundary = "---------" . str_replace(".", "", microtime());
	$reqbody  = "--$boundary\r\n"
			  . "Content-Disposition: form-data; name=\"$field\"; filename=\"$fname\"\r\n"
			  . "Content-Type: $mime\r\n\r\n"
			  . file_get_contents($fname) . "\r\n"
			  . "--$boundary--\r\n";
	$bodylen  = strlen($reqbody);
	$reqhead  = "POST $path HTTP/1.1\r\n"
			  . "Host: localhost\r\n"
			  . "Content-Type: multipart/form-data; boundary=$boundary\r\n"
			  . "Content-Length: $bodylen\r\n"
			  . "Connection: Close\r\n\r\n";
	
	/* open socket connection to remote host on port 80 */
	$fp = fsockopen($host, 80, $errno, $errmsg, 30);
	
	/* check the connection */
	if (!$fp) {
		print "Cannot connect to $host!\n";
		return false;
	}
	
	/* send request */
	fwrite($fp, $reqhead);
	fwrite($fp, $reqbody);
	
	/* read response */
	$res = "";
	while(!feof($fp)) {
		$res .= fgets($fp, 4096);
	}		
	fclose($fp);

	/* separate header and body */
	$neck = strpos($res, "\r\n\r\n");
	$head = substr($res, 0, $neck);
	$body = substr($res, $neck+4);

	/* check HTTP status */
	$lines = explode("\r\n", $head);
	preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $lines[0], $m);
	$status = $m[2];
	
	if ($status == 200) {
		return(true);
	} else {
		return(false);
	}
}
//############ TEST ################
	
    $remote = "http://s3.amazonaws.com/MinecraftSkins/cowboy80.png";
    $local  = "/temp/test.png";
	echo $local;
	echo $remote;
    $res = get_file($remote, $local);
    if ($res) {
        echo 'file saved.';
    } else {
        echo 'something went wrong.';
    }
	echo "fin";
    ?>
