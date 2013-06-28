<?php

/**
 * Class handles the password operations basing on user email.
 */
class eZForgotPasswordGenerator
{
    private $user = null;

    // statuses for password generate operation
    const PASSWORD_NOT_MATCH = 0;
    const PASSWORD_CHANGED = 1;
    const PASSWORD_TOO_SHORT = 2;

    /**
     * Constructor needs to have at least one of the possible parameters - either email addres or hash code
     * @param string|null $user_email
     * @param string|null $hash
     * @throws Exception
     */
    public function __construct( $user_email = null, $hash = null )
    {
        if ( !is_null( $user_email ) )
        {
            $this->user = $this->getUserByEmail( $user_email );
        }
        elseif( !is_null( $hash ) )
        {
            $this->user = $this->getUserByHash( $hash );
        }

        if ( is_null( $this->user ) )
        {
            throw new eZFPMissingInputException( 'Please set the user object before using password generator class.' );
        }
    }

    /**
     * Method generates the hash value
     * @return type
     */
    private function generateHash()
    {
        $ini = eZINI::instance( 'ezforgotpassword.ini' );
        return hash_hmac( 'md5', $this->user->attribute( 'email' ) . microtime(), $ini->variable( 'MainSettings', 'Md5Key' ) );
    }

    /**
     * Validates given email address and returns the user object
     * @param string $user_email
     * @return eZUser
     * @throws Exception
     */
    private function getUserByEmail( $user_email )
    {
        if ( filter_var( $user_email, FILTER_VALIDATE_EMAIL ) === false )
        {
            throw new eZFPIncorrectEmailException( 'Incorrect email address.' );
        }

        $user = eZUser::fetchByEmail( $user_email );
        if ( is_null( $user ) )
        {
            throw new eZFPNotExistingEmailException( 'There is no user with given email address.' );
        }

        return $user;
    }

    /**
     * Checks whether given hash exists and returns the user object
     * @param string $hash
     * @return eZUser
     * @throws Exception
     */
    private function getUserByHash( $hash )
    {
        $password_entry = eZForgotPassword::fetchByKey( $hash );
        if ( is_null( $password_entry ) )
        {
            throw new eZFPIncorrectHashException( 'Incorrect hash code.' );
        }

        $user = eZUser::fetch( $password_entry->attribute( 'user_id' ) );

        if ( is_null( $user ) )
        {
            throw new eZFPMissingHashUserException( 'User connected with hash code does not exist.' );
        }

        return $user;
    }

    /**
     * Send link operation, validates the email address and sends an email message. Returns boolean status.
     * @return bool
     */
    public function sendLink()
    {
        $status = false;

        // generate hash and store it in database
        $hash = $this->generateHash();
        $password_entry = eZForgotPassword::createNew( $this->user->id(), $hash, time() );
        $password_entry->store();

        $tpl    = eZTemplate::factory();
        $mail   = new eZMail();

        // render email template and send the message
        $tpl->setVariable( 'link', 'ezforgotpassword/generate/' . $hash );
        $mail->setContentType( 'text/html' );
        $mail->setBody( $tpl->fetch( 'design:ezforgotpassword/mail/generate_link.tpl' ) );
        $mail->addReceiver( $this->user->attribute( 'email' ) );
        $mail->setSubject( $tpl->variable( 'subject' ) );

        if ( eZMailTransport::send( $mail ) )
        {
            $status = true;
        }

        return $status;
    }

    /**
     * setting new password for the current user
     * @param string $new_password
     * @param string $repeat_password
     * @return int
     */
    public function setNewPassword( $new_password, $repeat_password )
    {
        if ( $new_password !== $repeat_password )
        {
            return self::PASSWORD_NOT_MATCH;
        }
        elseif ( strlen( $new_password ) < eZINI::instance( 'site.ini' )->variable( 'UserSettings', 'MinPasswordLength' ) )
        {
            return self::PASSWORD_TOO_SHORT;
        }

        $this->user->setAttribute( 'password_hash', eZUser::createHash( $this->user->attribute( 'login' ), $new_password, eZUser::site(), eZUser::hashType() ) );
        $this->user->store();
        $this->removeHash();

        return self::PASSWORD_CHANGED;
    }

    /**
     * Method removes all hashes that belongs to current user
     */
    private function removeHash()
    {
        eZForgotPassword::removeByUserID( $this->user->id() );
    }
}