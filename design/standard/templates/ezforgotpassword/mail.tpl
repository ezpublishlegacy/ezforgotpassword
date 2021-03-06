{* Template renders `mail` view of ezforgotpassword module.
@param string $user_email
@param bool $correct_email
@param bool $message_sent
*}

<div class="main-column">
    <div class="standard-form">

        {if $message_sent}
            <p>
                {"An email has been sent to the following address: %1. It contains a link you need to click to generate new password."|i18n( 'ezforgotpassword/mail', '', array( $user_email ) )}
            </p>
        {else}
            {if not( $correct_email )}
                <div class="warning">
                    <h2>{"There is no registered user with that email address."|i18n( 'ezforgotpassword/mail' )}</h2>
                </div>
            {elseif and( $user_email, not( $message_sent ) )}
                <div class="warning">
                    <h2>{"An error ocurred when sending the email message. Please try again later."|i18n( 'ezforgotpassword/mail' )}</h2>
                </div>
            {/if}

            <form method="post" name="ezforgotpassword" action={"/ezforgotpassword/mail"|ezurl()}>
                <div class="attribute-header">
                    <h1 class="long">{"Have you forgotten your password?"|i18n( 'ezforgotpassword/mail' )}</h1>
                </div>
                <p>
                    {"If you have forgotten your password, enter your email address and we will send you the instruction how to generate new password."|i18n( 'ezforgotpassword/mail' )}
                </p>
                <div class="block">
                    <label for="email">{"Email"|i18n( 'ezforgotpassword/mail' )}:</label>
                    <div class="labelbreak"></div>
                    <input class="halfbox" type="text" value="{$user_email|wash( 'email' )}" name="user_email" size="40" value="" />
                </div>
                <div class="buttonblock">
                    <input class="button dark" type="submit" value="{'Send instruction'|i18n( 'ezforgotpassword/mail' )}" />
                    <span class="button-end dark"></span>
                </div>
            </form>
        {/if}
    </div>
</div>