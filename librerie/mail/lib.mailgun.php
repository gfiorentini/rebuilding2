<?php

/*
	https://documentation.mailgun.com/en/latest/api-sending.html#sending
	
	$to = You can use commas to separate multiple recipients.
	$toname
	$mailfrom
	$mailfromname
	$subject
	$html
	$text
	$tag
	$replyto
	$attachments=null
	$cc = You can use commas to separate multiple recipients.
	$bcc = You can use commas to separate multiple recipients.
	$flddomain
	$idgen_mail_massiva_invio
*/

function sendmailbymailgun($to,$toname,$mailfrom,$mailfromname,$subject,$html,$text,$tag,$replyto,$attachments=null,$cc=null,$bcc=null,$flddomain=null,$idgen_mail_massiva_invio=null,$ideso_comunicazione_massiva=null)
{
	/*
	$db_mail = new DB_Sql();
	$db_mail->Database = DATABASE_NAME;
	$db_mail->User     = DATABASE_USER;
	$db_mail->Password = DATABASE_PASSWORD;
	$db_mail->Host     = DATABASE_HOST;
	*/
	$array_data = array(
		'from'=> $mailfromname.'<'.$mailfrom.'>',
		'to'=>$toname.'<'.$to.'>',
		'subject'=>$subject,
		'html'=>$html,
		'text'=>$text,
		//'o:tracking'=>'yes',
		//'o:tracking-clicks'=>'yes',
		'o:tracking-opens'=>'yes',
		'o:tag'=>$tag,
		'h:Reply-To'=>$replyto,
		//'X-Mailgun-Track-Clicks'=>'no'
	);

	if(!empty($attachments))
	{
		$attachments_array=explode('|', $attachments);

		$attachments_counter=1;
		foreach($attachments_array as $attachment)
		{	
			if(file_exists($attachment))
			{
				$array_data["attachment[$attachments_counter]"]=curl_file_create($attachment);
				$attachments_counter++;
			}
		}
	}

	if(!empty($cc))
		$array_data['cc']=$cc;


	if(!empty($bcc))
		$array_data['bcc']=$bcc;


	if(empty($flddomain))
	{
		//$flddomain="regione.marche.it"; 
		$flddomain="iccs.it"; 
	}
	

	

	$session = curl_init('https://api.mailgun.net/v3/'.$flddomain.'/messages');

	//print_r_formatted($array_data);
	//echo "KEY: ".MAILGUN_KEY;
	$key='key-2cf7a39ecfca68a4a9dee68c10be2d66';
	curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($session, CURLOPT_USERPWD, 'api:'.$key);
	curl_setopt($session, CURLOPT_POST, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($session);
	$information = curl_getinfo($session);

	$info = curl_getinfo($session);

	$error = curl_error($session);
	/*
	echo "RESPONSE:<br>";
	print_r_formatted($response);

	echo "INFO:<br>";
	print_r_formatted($info);

	echo "ERROR:<br>";
	print_r_formatted($error);
	*/
	curl_close($session);
	$results = json_decode($response, true);
	//print_r_formatted($results);

	$flddestinatario_idutente='';
	$flddestinatario_nominativo=db_string($toname);
	$flddestinatario_mail=db_string($to);
	$cc=db_string($cc);
	$bcc=db_string($bcc);
	$fldmittente_nominativo=db_string($mailfromname);
	$fldmittente_mail=db_string($mailfrom);
	$fldsubject=db_string($subject);
	$fldbody=db_string($html);
	$fldallegati=db_string($attachments);
	$flddata=date("Y-m-d");
	$fldora=date("H:i:s");
	$response=db_string($response);
	//print_r($response);
	/*
	$sSQL="INSERT INTO ".DBNAME_A.".gen_mail_temp 
	(destinatario_idutente, destinatario_nominativo, destinatario_mail,
	cc,bcc,
	mittente_nominativo, mittente_mail, subject, 
	body,allegati,
	data, ora, result,
	idgen_mail_massiva_invio, domain, ideso_comunicazione_massiva)
	VALUES 
	('$flddestinatario_idutente','$flddestinatario_nominativo','$flddestinatario_mail',
	'$cc','$bcc',
	'$fldmittente_nominativo','$fldmittente_mail','$fldsubject',
	'$fldbody','$fldallegati',
	'$flddata','$fldora','$response',
	'$idgen_mail_massiva_invio', '$flddomain', '$ideso_comunicazione_massiva')";
	$db_mail->query($sSQL);		
	*/
	return $results;
}

?>