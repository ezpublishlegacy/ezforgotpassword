<?php

$http       = eZHTTPTool::instance();
$tpl        = eZTemplate::factory();
$user_email = '';
$status     = '';

if ( $http->hasPostVariable( 'user_email' ) )
{
    $user_email = $http->postVariable( 'user_email' );
    try
    {
        // try to create an object base on given email address
        $generator = eZForgotPasswordGenerator::getInstanceByEmail( $user_email );
    }
    // exception is throwm when someting goes wrong
    catch( Exception $e )
    {
        $status = 'WRONG_EMAIL';
    }

    if ( empty( $status ) )
    {
        $status = $generator->sendLink();
    }
}

$tpl->setVariable( 'user_email', $user_email );
$tpl->setVariable( 'status', $status );

$Result['content'] = $tpl->fetch( 'design:ezforgotpassword/mail.tpl' );
$Result['path']    = array( array( 'text' => ezpI18n::tr( 'ezforgotpassword/mail', 'Forgot password' ) ) );