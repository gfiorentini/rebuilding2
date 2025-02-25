<?php 

if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

require(__DIR__."/lib.mailgun.php");
/*
function sendMAIL($aInformazioni, $flag_reply=1, $fldflag_confirm=0)
{
    if(is_array($aInformazioni))
	{
		$flag_success=false;

		//print_r($aInformazioni);

		$fldemail=$aInformazioni[0];
		$fldoggetto=$aInformazioni[1];
		$fldtesto=$aInformazioni[2];
        $html=$fldtesto;
        $testo=$fldtesto;
		$fldallegati=$aInformazioni[3];
        $aDESTINATARI=explode(";",$fldemail);
        $nome_destinatario='';
        $mail_mittente="noreply.rebuilding@regione.marche.it";
        $nome_mittente='Regione Marche - Rebuilding';
        $tag=$_SERVER['HTTP_HOST'];
        $replyto="";
		$cc="";
		$bcc="";
        $domain='';
        $$idgen_mail_massiva_invio='';
        $fldideso_comunicazione_massiva='';
        //nel caso in cui l'email vada inviata a più destinatari il separatore è il ";", il sistema risponde con l'esito dell'ultimo invio, la possibilità che una stessa email abbia più di un destinatario è remota, al momento solo 1 centro cottura di senigallia
        foreach($aDESTINATARI as $mail_destinatario)
        {
            //$mail_destinatario="claudio.milani@iccs.it";
            if(!empty($mail_destinatario))
            {
                $result=sendmailbymailgun($mail_destinatario,$nome_destinatario,$mail_mittente,$nome_mittente,$fldoggetto,$html,$testo,$tag,$replyto,$fldallegati,$cc,$bcc,$domain,$idgen_mail_massiva_invio,$fldideso_comunicazione_massiva);
                if($result["message"]=="Queued. Thank you.")
                    $flag_success=true;   
                else
                    $flag_success=$result;   
            }
        }	
       
        return $flag_success;
       
    }
}
*/
function sendMAIL($aInformazioni, $flag_reply=1, $fldflag_confirm=0)
{
    $result=false;
    if(is_array($aInformazioni))
	{

        //$email_mittente="noreply.rebuilding@regione.marche.it";
        $email_mittente="direzionepolitichesociali@regione.marche.it";
        $fldsmtp="webmail.regione.marche.it";
        $fldsmtp_porta="25";
        //$fldusername="noreply@iccs.it";
        //$fldpassword="MpWscsSTuR2jGr!";	

        /*
            $email_mittente="noreply@iccs.it";
            $fldsmtp="smtps.aruba.it";
            $fldsmtp_porta="465";
            $fldusername="noreply@iccs.it";
            $fldpassword="MpWscsSTuR2jGr!";	
        */
        try 
        {
            require_once(__DIR__."/class.phpmailer.php");
            $mail = new PHPMailer(true); //New instance, with exceptions enabled
                                
            $mail->IsSMTP();                           // tell the class to use SMTP
            if ($fldsmtp_porta=="25")
                $mail->SMTPAuth   = false;                 // enable SMTP authentication
            else 
                $mail->SMTPAuth   = true;                  // enable SMTP authentication

            $mail->Port       = $fldsmtp_porta;   // set the SMTP server port
            $mail->Host       = $fldsmtp;           // SMTP server
            //$mail->Username   = $fldusername;     // SMTP server username
            //$mail->Password   = $fldpassword;            // SMTP server password
            if ($fldsmtp_porta!="25")
                $mail->SMTPSecure = "ssl";

            //$mail->IsSendmail();  // tell the class to use Sendmail

            if(!empty($flag_reply))
                $mail->AddReplyTo($email_mittente,$email_mittente);

            $mail->From       = $email_mittente;
            $mail->FromName   = "Direzione Politiche Sociali";

            if(!empty($fldflag_confirm))
                $mail->ConfirmReadingTo = $email_mittente;

            $to = $aInformazioni[0];
            //$to="giacomo.fiorentini@regione.marche.it";
            $mail->AddAddress($to);
            $mail->Subject  = $aInformazioni[1];
            $mail->AltBody  = $aInformazioni[2]; // optional, comment out and test
            $mail->WordWrap = 80; // set word wrap

            $mail->MsgHTML($aInformazioni[2]);
            $mail->IsHTML(true); // send as HTML
            if($aInformazioni[3])
            {
                $aAllegati=explode("|",$aInformazioni[3]);
                $acount=count($aAllegati)-1;
                for ($i=0;$i<=$acount;$i++)
                {
                    $mail->AddAttachment($aAllegati[$i]);
                }
            }

            $mail->Send();

            $result=true;       	
        } 
        catch (phpmailerException $e) 
        {
            //echo $e->errorMessage();
            return $e->errorMessage();
            //return $result;
        }
    }
    /*
    if($result)
    {
        global $db;

        $data=dataodierna();
        $orario=oraodierna();
        
	    $email_destinatario=$db->escape_text($aInformazioni[0]);
	    $email_oggetto=$db->escape_text($aInformazioni[1]);
	    $email_testo=$db->escape_text($aInformazioni[2]);
	    $email_allegati=$db->escape_text($aInformazioni[3]);
	    $email_idanagrafica=$db->escape_text($aInformazioni[4]);

        $sSQL="INSERT INTO dara_email (
            email_idanagrafica, 
            email_mittente, 
            email_destinatario,
            email_cc,
            email_bcc, 
            email_oggetto, 
            email_testo,
            email_allegati,
            email_datainvio, 
            email_orainvio
        )
        VALUES 
        (
            '$email_idanagrafica',
            '$email_mittente',
            '$email_destinatario',
            '$email_cc',
            '$email_bcc',
            '$email_oggetto',
            '$email_testo',
            '$email_allegati',
            '$data',
            '$orario'
        )";
        $db->query($sSQL);		
    }   
    */

    return $result;
}
?>
