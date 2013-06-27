<?php

/**
 * Class handles the password operations basing on user email.
 */
class eZForgotPasswordGenerator
{
    private static $user;
    private static $obj = null;

    /**
     * Blocked constructor - only for internal use
     */
    private function __construct() {}

    /**
     * Method generates the hash value
     * @return type
     */
    private function generateHash()
    {
        $ini = eZINI::instance( 'ezforgotpassword.ini' );
        return hash_hmac( 'md5', self::$user->attribute( 'email' ) . microtime(), $ini->variable( 'MainSettings', 'Md5Key' ) );
    }

    /**
     * Sets the user_email for an object and returns the object
     * @param string $user_email
     * @return eZForgotPasswordGenerator
     */
    public static function getInstanceByEmail( $user_email )
    {
        if ( is_null( self::$obj ) )
        {
            self::$obj  = new self();
            self::$user = eZUser::fetchByEmail( $user_email );

            if ( filter_var( $user_email, FILTER_VALIDATE_EMAIL ) === false || is_null( self::$user ) )
            {
                throw new Exception( 'Incorrect email address.' );
            }
        }

        return self::$obj;
    }

    public static function getInstanceByHash( $hash )
    {
        if ( is_null( self::$obj ) )
        {
            self::$obj = new self();
        }

        return self::$obj;
    }

    /**
     * Send link operation, validates the email address and sends him an email message. Returns status.
     * @return string (WRONG_EMAIL|MESSAGE_NOT_SENT|MESSAGE_SENT)
     */
    public function sendLink()
    {
        $status = 'MESSAGE_NOT_SENT';

        // generate hash and store it in database
        $hash = $this->generateHash();
        $password_entry = eZForgotPassword::createNew( self::$user->id(), $hash, time() );
        $password_entry->store();

        $tpl    = eZTemplate::factory();
        $mail   = new eZMail();
        $ini    = eZINI::instance( 'ezforgotpassword.ini' );

        // render email template and send the message
        $tpl->setVariable( 'link', 'ezforgotpassword/generate/' . $hash );
        $mail->setSubject( $ini->variable( 'MainSettings', 'EmailSubject' ) );
        $mail->setContentType( 'text/html' );
        $mail->setBody( $tpl->fetch( 'design:ezforgotpassword/mail/generate_link.tpl' ) );
        $mail->addReceiver( self::$user->attribute( 'email' ) );

        if ( eZMailTransport::send( $mail ) )
        {
            $status = 'MESSAGE_SENT';
        }

        return $status;
    }
}