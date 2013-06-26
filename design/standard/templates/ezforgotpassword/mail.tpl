{* Template renders `mail` view of ezforgotpassword module. Params:

*}

<div class="main-column">
    <div class="standard-form">
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
                <input class="halfbox" type="text" name="user_email" size="40" value="" />
            </div>
            <div class="buttonblock">
                <input class="button dark" type="submit" value="{'Send instruction'|i18n( 'ezforgotpassword/mail' )}" />
                <span class="button-end dark"></span>
            </div>
        </form>
    </div>
</div>