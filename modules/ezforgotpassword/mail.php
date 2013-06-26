<?php

$http       = eZHTTPTool::instance();
$tpl        = eZTemplate::factory();
$user_email = '';
$status     = '';

if ( $http->hasPostVariable( 'user_email' ) )
{
    $user_email = $http->postVariable( 'user_email' );
    $generator  = new eZForgotPasswordGenerator( $user_email );
    $status     = $generator->sendLink();
}

$tpl->setVariable( 'user_email', $user_email );
$tpl->setVariable( 'status', $status );

$Result['content'] = $tpl->fetch( 'design:ezforgotpassword/mail.tpl' );
$Result['path']    = array( array( 'text' => ezpI18n::tr( 'ezforgotpassword/mail', 'Forgot password' ) ) );