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
 */

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

    $req = new SoapClient('https://spid.comune-online.it/AuthServiceSPID/services/AuthService?wsdl',$options);    

#    echo $req;

#    $response = $sClient->loginVerify($param1, $param2);    
#
     var_dump($req);
}
catch(SoapFault $e)
{
    var_dump($e);
}

echo $req;

?>
