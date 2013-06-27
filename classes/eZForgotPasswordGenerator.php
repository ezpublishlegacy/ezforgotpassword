<?php

/**
 * Class handles the password operations basing on user email.
 */
class eZForgotPasswordGenerator
{
    private $user_email;

    /**
     * Constructor - needs an user email
     * @param string $user_email
     */
    public function __construct( $user_email )
    {
        $this->user_email = $user_email;
    }

    /**
     * Method generates the hash value
     * @return type
     */
    private function generateHash()
    {
        $ini = eZINI::instance( 'ezforgotpassword.ini' );
        return hash_hmac( 'md5', $this->user_email . microtime(), $ini->variable( 'MainSettings', 'Md5Key' ) );
    }

    /**
     * Send link operation, validates the email address and sends him an email message. Returns status.
     * @return string (WRONG_EMAIL|MESSAGE_NOT_SENT|MESSAGE_SENT)
     */
    public function sendLink()
    {
        $status = '';

        // given email address is not correct
        if ( filter_var( $this->user_email, FILTER_VALIDATE_EMAIL ) === false )
        {
            $status = 'WRONG_EMAIL';
        }
        else
        {
            // check whether user with given email address exists
            $user = eZUser::fetchByEmail( $this->user_email );
            if ( is_null( $user ) )
            {
                $status = 'WRONG_EMAIL';
            }
            else
            {
                // generate hash and store it in database
                $hash = $this->generateHash();
                $password_entry = eZForgotPassword::createNew( $user->id(), $hash, time() );
                $password_entry->store();
                
                $tpl    = eZTemplate::factory();
                $mail   = new eZMail();
                $ini    = eZINI::instance( 'ezforgotpassword.ini' );

                // render email template and send the message
                $tpl->setVariable( 'link', 'ezforgotpassword/generate/' . $hash );
                $mail->setSubject( $ini->variable( 'MainSettings', 'EmailSubject' ) );
                $mail->setContentType( 'text/html' );
                $mail->setBody( $tpl->fetch( 'design:ezforgotpassword/mail/generate_link.tpl' ) );
                $mail->addReceiver( $this->user_email );

                $status = 'MESSAGE_NOT_SENT';
                if ( eZMailTransport::send( $mail ) )
                {
                    $status = 'MESSAGE_SENT';
                }
            }
        }

        return $status;
    }
}