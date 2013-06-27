<?php

$Module = $Params['Module'];
$hash   = isset( $Params['hash'] ) ? $Params['hash'] : '';
$tpl    = eZTemplate::factory();
$http   = eZHTTPTool::instance();

// made the hash a required parameter
if ( empty( $hash ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

try
{
    $generator = eZForgotPasswordGenerator::getInstanceByHash( $hash );
}
catch( Exception $e )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

$status = '';
if ( $http->hasPostVariable( 'new_password' ) )
{
    $status = $generator->setNewPassword( $http->postVariable( 'new_password' ), $http->postVariable( 'repeat_password' ) );
}

$tpl->setVariable( 'hash', $hash );
$tpl->setVariable( 'status', $status );

$Result['content'] = $tpl->fetch( 'design:ezforgotpassword/generate.tpl' );
$Result['path']    = array( array( 'text' => ezpI18n::tr( 'ezforgotpassword/generate', 'Generate new password' ) ) );