<?php
class Push extends CI_Controller
{
    public  $iOSToken;
    public  $androidToken;
    public  $production;
    public  $passphrase;

    private $connection;

    public function __construct()
    {
    }

    public function connectApple()
    {
        if(!$this->connection)
        {
            $ctx = stream_context_create();
            $err = '';
            $errstr = '';
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
            //AppleCertificates directory should be a sibling to public_html & CodeIgniter folders.
            if($this->production)
            {
                stream_context_set_option($ctx, 'ssl', 'local_cert', '../AppleCertificates/demoproduction.pem');
                $this->connection = stream_socket_client("ssl://gateway.push.apple.com:2195",$err,$errstr,60,STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,$ctx);
                echo "production\n";
            }
            else
            {
                stream_context_set_option($ctx, 'ssl', 'local_cert', '../AppleCertificates/demodevelopment.pem');
                $this->connection = stream_socket_client("ssl://gateway.sandbox.push.apple.com:2195",$err,$errstr,60,STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,$ctx);
            }
            if(!$this->connection)
                echo "Error: failed to connect - $err $errstr\n";
            else
                echo "Connected successfully\n";
        }
    }
    public function disconnectApple()
    {
        if($this->connection)
        {
            fclose($this->connection);
            $this->connection = null;
            echo "Disconnected successfully\n";
        }
    }
    public function writeNotificationToStream($token,$message,$attempt = 0)
    {
        if($attempt == 3)
            return false;
        $body['aps'] = array(
            'alert'=>$message,
            'sound'=>'default'
            );
        $payload = json_encode($body);
        $msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
        $result = fwrite($this->connection, $msg, strlen($msg));
        if(!$result)
        {
            echo "Failed to send message: ";
            $this->disconnectApple();
            $this->connectApple();
            return $this->writeNotificationToStream($token,$message,$attempt+1);
        }
        echo $msg . "\n";
        return true;
    }

	public function notify($message)
	{
		if($this->iOSToken!=null)
		{
			$this->push_notify_apple($message);
			return;
		}
		if($this->androidToken!=null)
		{
			$this->push_notify_android($message);
		}

	}
	 /**
     * PushApple
     *
     * @access  public
     * @param   int userid
     * @param   string message
     * @return  null
     */
    private function push_notify_apple($message)
    {
        // $this->post('to'); die();
        // Put your device token here (without spaces):
        if($this->iOSToken==null) //this user doesn't have a registered apple device, do nothing
            return;
        // Put your private key's passphrase here:
        $passphrase = 'radhaconsulting';

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'demoproduction.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
            );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $this->iOSToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
    }
    private function push_notify_android($message)
    {
        // Replace with real BROWSER API key from Google APIs
        $apiKey = "AIzaSyBWCXZwONB0rIa5_yHx1Q_p4QLR9IN-DJU";

        // Replace with real client registration IDs 
        $registrationIDs = array( $this->androidToken );
        // Message to be sent

        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
                        'registration_ids'  => $registrationIDs,
                        'data'              => array( "message" => $message ),
                        );

        $headers = array( 
                            'Authorization: key=' . $apiKey,
                            'Content-Type: application/json'
                        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $url );

        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        // echo $result;
    }
}
