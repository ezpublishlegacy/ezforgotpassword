<?php

// eZForgotPassword extension tests

// Run with: > php tests/runtests.php --dsn mysqli://root:root@localhost/eztests --filter="eZForgotPasswordTest" --db-per-test
// (assumes the database 'eztests' have been created)

class ezForgotPasswordTest extends ezpDatabaseTestCase
{
    public function __construct( $name = NULL, array $data = array(), $dataName = '' )
    {
        parent::__construct( $name, $data, $dataName );
        $this->setName( 'ezForgot password extension tests.' );
    }

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @expectedException eZFPMissingInputException
     */
    public function testMissingInput()
    {
        new eZForgotPasswordGenerator();
    }

    
}