<?php

$tpl = eZTemplate::factory();

$Result['content'] = $tpl->fetch( 'design:ezforgotpassword/mail.tpl' );
$Result['path']    = array( array( 'text' => ezpI18n::tr( 'ezforgotpassword/mail', 'Forgot password' ) ) );