<?php define( '_VALID_ENTRADA', 1 );
 //require_once "/usr/share/pear/Mail.php";
 require_once "Mail.php";
 require_once "configuration.php";

 $from = "TPV <tpv@amfglobalitems.com>";
# $from = "soporte <soporte@amfglobalitems.com>";
 $cc = "Julio Toirac <jtoirac@gmail.com>";
 //$to = "Kadir Femenias <kadir@tur.cu>";
 $to = "kadir <kadir.femenias@gmail.com>";
 $subject = "Subject Sujetado!";
 //$body = "Hi,\n\nHow are you? Cojones que calor!!!";
 $body = "Hi,\n\nHow are you?";

 function RFCDate() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        $tz = ($tz/3600)*100 + ($tz%3600)/60;
        $result = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);

        return $result;
 }

 $date = RFCDate();

 $host = "mail.amfglobalitems.com";
 $username = "tpv@amfglobalitems.com";
 $password = "fQjx8*49";
# $password = "ne6MGzMqMVGEh3B3";
   
# $host = "mail.amfglobalitems.com";
# $username = "soporte@amfglobalitems.com";
# $password = "ViewSonic2*1*";

 $headers = array ('MIME-Version' => "1.0",
   'Date' => $date,
   'Message-ID' => "<" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">",
   'Subject' => $subject,
   'From' => $from,
   'To' => $to,
   'Cc' => $cc,
   'Content-type' => "text/html; charset=iso-8859-1\r\n\r\n");
 $smtp = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
     'username' => $username,
     'password' => $password));

 //$mail = $smtp->send($to, $headers, $body);

 if (PEAR::isError($mail)) {
   echo("<p>" . $mail->getMessage() . "</p>");
  } else {
   echo("<p>Message successfully sent!</p>");
  }
  
  echo _PATH_SITIO;
 ?>
