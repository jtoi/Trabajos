<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

  $myEmailLabel = $params->get('email_label', 'Email:');
  $mySubjectLabel = $params->get('subject_label', 'Subject:');
  $myMessageLabel = $params->get('message_label', 'Message:');

  $recipient = $params->get('email_recipient', '');
  $buttonText = $params->get('button_text', 'Send Message');

  $pageText = $params->get('page_text', 'Thank you for your contact.');
  $thanksTextColor = $params->get('thank_text_color', '#FF0000');
  $errorText = $params->get('errot_text', 'Your message could not be sent. Please try again.');

  $noEmail = $params->get('no_email', 'Please write your email');
  $invalidEmail = $params->get('invalid_email', 'Please write a valid email');

  $fromName = @$params->get('from_name', 'Rapid Contact');
  $fromEmail = @$params->get('from_email', 'rapid_contact@yoursite.com');

  $emailWidth = $params->get('email_width', '15');
  $subjectWidth = $params->get('subject_width', '15');
  $messageWidth = $params->get('message_width', '12');
  $buttonWidth = $params->get('button_width', '100');
  
  $enable_anti_spam = $params->get('enable_anti_spam', false);
  $myAntiSpamQuestion = $params->get('anti_spam_q', 'How many eyes has a typical person?');
  $myAntiSpamAnswer = $params->get('anti_spam_a', '2');
  
  $enable_recaptcha   = $params->get('enable_recaptcha', false);
  $recaptcha_public   = $params->get('recaptcha_public_key', false);
  $recaptcha_private  = $params->get('recaptcha_private_key', false);
  
  $disable_https = $params->get('disable_https', false);
     
  $mod_class_suffix = $params->get('moduleclass_sfx', '');

  $pre_text = $params->get('pre_text', '');
  
  // include the recaptcha library if necessary
  if ($enable_recaptcha)
  {
    require_once('lib/recaptchalib.php');
    $resp = recaptcha_check_answer($recaptcha_private,
                                  $_SERVER["REMOTE_ADDR"],
                                  $_POST["recaptcha_challenge_field"],
                                  $_POST["recaptcha_response_field"]);
  }
  $recaptcha_error = null;

  $exact_url = $params->get('exact_url', true);
  if (!$exact_url) {
    $url = JURI::current();
  }
  else {
    if (!$disable_https) {
    $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
    else {
    $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
  }
  
  $myError = '';

  if (isset($_POST["rp_email"]))
  {
    if ($enable_anti_spam && $_POST["rp_anti_spam_answer"] != $myAntiSpamAnswer)  // anti spam question
    {
      $myError = '<span style="color: #f00;">' . JText::_('Wrong anti-spam answer') . '</span>';
    }
    elseif ($enable_recaptcha && !$resp->is_valid) // reCaptcha spam
    {
      $myError = '<span style="color: #f00;">' . JText::_('The reCAPTCHA wasn\'t entered correctly. Go back and try it again.' . '(reCAPTCHA said: ' . $resp->error . ')') . '</span>';
      $recaptcha_error = $resp->error;
    }
    
    if ($_POST["rp_email"] === "")
    {
      $myError = '<span style="color: #f00;">' . $noEmail . '</span>';
    }
    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $_POST["rp_email"]))
    {
      $myError = '<span style="color: #f00;">' . $invalidEmail . '</span>';
    }

    if ($myError == '') {
    $mySubject = $_POST["rp_subject"];
    $myMessage = 'You received a message from '. $_POST["rp_email"] ."\n\n". $_POST["rp_message"];
    $mailSender = &JFactory::getMailer();
    $mailSender->addRecipient($recipient);

    $mailSender->setSender(array($fromEmail,$fromName));
    $mailSender->addReplyTo(array( $_POST["rp_email"], '' ));

    $mailSender->setSubject($mySubject);
    $mailSender->setBody($myMessage);

    if (!$mailSender->Send()) {
      $myReplacement = '<span style="color: #f00;">' . $errorText . '</span>';
      print $myReplacement;
      return true;
    }
    else {
      $myReplacement = '<span style="color: '.$thanksTextColor.';">' . $pageText . '</span>';
      print $myReplacement;
      return true;
    }

    }
  }

  if ($recipient === "") {
    $myReplacement = '<span style="color: #f00;">No recipient specified</span>';
    print $myReplacement;
    return true;
  }
  if ($myError != '') {
    print $myError;
  }
  print '<div class="rapid_contact ' . $mod_class_suffix . '"><form action="' . $url . '" method="post">' . "\n" .
      '<div class="rapid_contact intro_text ' . $mod_class_suffix . '">'.$pre_text.'</div>' . "\n";
  print '<table>';
  
  if ($enable_anti_spam) {
    print '<tr><td colspan="2">' . $myAntiSpamQuestion . '</td></tr><tr><td></td><td><input class="rapid_contact inputbox ' . $mod_class_suffix . '" type="text" name="rp_anti_spam_answer" size="' . $emailWidth . '"/></td></tr>' . "\n";
  }
  
  print '<tr><td>' . $myEmailLabel . '</td><td><input class="rapid_contact inputbox ' . $mod_class_suffix . '" type="text" name="rp_email" size="' . $emailWidth . '"/></td></tr>' . "\n";
  print '<tr><td>' . $mySubjectLabel . '</td><td><input class="rapid_contact inputbox ' . $mod_class_suffix . '" type="text" name="rp_subject" size="' . $subjectWidth . '"/></td></tr>' . "\n";
  print '<tr><td valign="top">' . $myMessageLabel . '</td><td><textarea class="rapid_contact textarea ' . $mod_class_suffix . '" type="text" name="rp_message" cols="' . $messageWidth . '" rows="4"></textarea></td></tr>' . "\n";
  
  if ($enable_recaptcha)
  {
    print '<tr><td>&nbsp;</td><td>' . recaptcha_get_html($recaptcha_public, $recaptcha_error) . '</td></tr>';
  }
  print '<tr><td colspan="2"><input class="rapid_contact button ' . $mod_class_suffix . '" type="submit" value="' . $buttonText . '" style="width: ' . $buttonWidth . '%"/></td></tr></table></form></div>' . "\n";
    
  return true;
