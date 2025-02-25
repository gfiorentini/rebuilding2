<?php
try
{

/*
$opts = array(
    'ssl' => array(
        'ciphers' => 'RC4-SHA',
        'verify_peer' => false,
        'verify_peer_name' => false
    )
);

// SOAP 1.2 client
$params = array(
    'encoding' => 'UTF-8',
    'verifypeer' => false,
    'verifyhost' => false,
    'soap_version' => SOAP_1_2,
    'trace' => 1,
    'exceptions' => 1,
    'connection_timeout' => 180,
    'stream_context' => stream_context_create($opts)
);
 

$options = array(
    'cache_wsdl' => 0,
    'trace' => 1,
    'stream_context' => stream_context_create(array(
          'ssl' => array(
               'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
          )
    )
  )
);
 
    $sClient = new SoapClient('https://spid.comune-online.it/AuthServiceSPID/services/AuthService?wsdl',$options);    
 */
#	echo "------------------- wsdl start here ---------------------------";
#	$myxml = file_get_contents('https://spid.comune-online.it/AuthServiceSPID/services/AuthService?wsdl');
	
	$myxml = file_get_contents('http://www.google.it');
	#	echo "################### wsdl end here #############################";
	
	#	var_dump($xml);

	

#    $response = $sClient->loginVerify($param1, $param2);    
#
#    var_dump($response);
}
catch(SoapFault $e)
{
	echo "---------exception---------";
	echo $e;
	var_dump($e);
	echo "---------exception---------";
}
?>
<textarea rows='20' cols='100'><?php echo $myxml ?> </textarea>

