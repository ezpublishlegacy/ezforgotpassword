<?php

class ezForgotPasswordTestSuite extends ezpDatabaseTestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->insertDefaultData = false;
        $this->setName( "eZForgotPassword extension test suite" );

        // Adding tests
        $this->addTestSuite( 'testMissingInput' );
    }

    public static function suite()
    {
        return new self();
    }
}