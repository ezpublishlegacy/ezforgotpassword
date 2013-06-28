<?php

$Module         = array( 'name' => 'ezforgotpassword' );
$ViewList       = array();
$FunctionList   = array(
    'generate'  => array()
);

$ViewList['mail'] = array(
    'script'    => 'mail.php',
    'functions' => array( 'generate' ),
);

$ViewList['generate'] = array(
    'script'    => 'generate.php',
    'functions' => array( 'generate' ),
    'params'    => array( 'hash' ),
);