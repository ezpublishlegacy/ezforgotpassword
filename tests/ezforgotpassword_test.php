<?php

// eZForgotPassword extension tests

// Run with: > php tests/runtests.php --dsn mysqli://root:password@localhost/eztests --filter="ezForgotPasswordTest" --db-per-test
// (assumes the database 'eztests' have been created)

class ezForgotPasswordTest extends ezpDatabaseTestCase
{
    const CORRECT_EMAIL = 'piotr.szczygiel@makingwaves.pl';

    public function __construct( $name = NULL, array $data = array(), $dataName = '' )
    {
        parent::__construct( $name, $data, $dataName );
        $this->setName( 'ezForgot password extension tests.' );
    }

    protected function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();
        parent::tearDown();
    }

    /**
     * Helper method
     * @return \eZUser
     */
    private function createNewUser()
    {
        $user = new eZUser( array(
            'login'                 => self::CORRECT_EMAIL,
            'email'                 => self::CORRECT_EMAIL,
            'password_hash'         => eZUser::createHash( self::CORRECT_EMAIL, 'test', eZUser::site(), eZUser::hashType() ),
            'password_hash_type'    => eZUser::hashType()
        ) );
        $user->store();
        return $user;
    }

    /**
     * @expectedException eZFPMissingInputException
     */
    public function testMissingInput()
    {
        new eZForgotPasswordGenerator();
    }

    public function testIncorrectEmailAddresses()
    {
        $count  = 0;
        $inputs = array(
            '', 'test', 'test@', 'test@test', '111', '111@', '111@111', 'at@@at.at', 'test\@test.te'
        );

        foreach ( $inputs as $input )
        {
            try
            {
                new eZForgotPasswordGenerator( $input );
            }
            catch ( eZFPIncorrectEmailException $e )
            {
                $count++;
            }
        }

        $this->assertCount( $count, $inputs );
    }

    /**
     * @expectedException eZFPNotExistingEmailException
     */
    public function testCorrectNotExistingEmailAddress()
    {
        new eZForgotPasswordGenerator( self::CORRECT_EMAIL );
    }

    public function testCorectExistingEmailAddress()
    {
        $this->createNewUser();
        $this->assertInstanceOf( 'eZForgotPasswordGenerator', new eZForgotPasswordGenerator( self::CORRECT_EMAIL ) );
    }

    /**
     * @expectedException eZFPIncorrectHashException
     */
    public function testIncorrectHash()
    {
        $hash = 'not-existing-hash-code';
        new eZForgotPasswordGenerator( null, $hash );
    }

    /**
     * @expectedException eZFPMissingHashUserException
     */
    public function testHashUserDoesntExist()
    {
        $hash           = md5( time() );
        $dummy_user_id  = 10000;
        $password_entry = eZForgotPassword::createNew( $dummy_user_id, $hash, time() );

        $password_entry->store();
        new eZForgotPasswordGenerator( null, $hash );
    }

    public function testPasswordsNotMatch()
    {
        $user       = $this->createNewUser();
        $generator  = new eZForgotPasswordGenerator( $user->attribute( 'email' ) );

        $this->assertEquals( $generator->setNewPassword( 'pass1', 'pass2' ), eZForgotPasswordGenerator::PASSWORD_NOT_MATCH );
    }

    public function testPasswordTooshort()
    {
        $user       = $this->createNewUser();
        $generator  = new eZForgotPasswordGenerator( $user->attribute( 'email' ) );

        $this->assertEquals( $generator->setNewPassword( '', '' ), eZForgotPasswordGenerator::PASSWORD_TOO_SHORT );
    }

    public function testPasswordChanged()
    {
        $user       = $this->createNewUser();
        $generator  = new eZForgotPasswordGenerator( $user->attribute( 'email' ) );

        $this->assertEquals( $generator->setNewPassword( 'pass', 'pass' ), eZForgotPasswordGenerator::PASSWORD_CHANGED );
    }

    public function testSendEmail()
    {
        $user       = $this->createNewUser();
        $generator  = new eZForgotPasswordGenerator( $user->attribute( 'email' ) );

        ezpINIHelper::setINISetting( 'site.ini', 'MailSettings', 'Transport', 'file' );
        $this->assertEquals( true, $generator->sendLink() );
    }
}