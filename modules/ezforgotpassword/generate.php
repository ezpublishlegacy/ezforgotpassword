<?php

$Module = $Params['Module'];
$hash   = isset( $Params['hash'] ) ? $Params['hash'] : '';
$tpl    = eZTemplate::factory();
$http   = eZHTTPTool::instance();
$status = null;

// made the hash a required parameter
if ( empty( $hash ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

try
{
    $generator = new eZForgotPasswordGenerator( null, $hash );
}
catch( Exception $e )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

if ( $http->hasPostVariable( 'new_password' ) )
{
    $status = $generator->setNewPassword( $http->postVariable( 'new_password' ), $http->postVariable( 'repeat_password' ) );
}

$tpl->setVariable( 'hash', $hash );
$tpl->setVariable( 'passwd_not_match', $status === eZForgotPasswordGenerator::PASSWORD_NOT_MATCH );
$tpl->setVariable( 'passwd_too_short', $status === eZForgotPasswordGenerator::PASSWORD_TOO_SHORT );
$tpl->setVariable( 'passwd_changed', $status === eZForgotPasswordGenerator::PASSWORD_CHANGED );

$Result['content'] = $tpl->fetch( 'design:ezforgotpassword/generate.tpl' );
$Result['path']    = array( array( 'text' => ezpI18n::tr( 'ezforgotpassword/generate', 'Generate new password' ) ) );