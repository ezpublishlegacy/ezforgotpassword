<?php

$http       = eZHTTPTool::instance();
$tpl        = eZTemplate::factory();
$user_email = '';

$correct_email  = true;
$message_sent   = false;

if ( $http->hasPostVariable( 'user_email' ) )
{
    $user_email = $http->postVariable( 'user_email' );
    try
    {
        // try to create an object base on given email address
        $generator = new eZForgotPasswordGenerator( $user_email );
    }
    // exception is throwm when someting goes wrong
    catch( Exception $e )
    {
        $correct_email = false;
    }

    if ( $correct_email )
    {
        $message_sent = $generator->sendLink();
    }
}

$tpl->setVariable( 'user_email', $user_email );
$tpl->setVariable( 'correct_email', $correct_email );
$tpl->setVariable( 'message_sent', $message_sent );

$Result['content'] = $tpl->fetch( 'design:ezforgotpassword/mail.tpl' );
$Result['path']    = array( array( 'text' => ezpI18n::tr( 'ezforgotpassword/mail', 'Forgot password' ) ) );