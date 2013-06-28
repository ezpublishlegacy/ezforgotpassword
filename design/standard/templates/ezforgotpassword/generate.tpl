{*
@param string $hash
@param bool $passwd_not_match
@param bool $passwd_too_short
@param bool $passwd_changed
*}

<div class="main-column">
    <div class="standard-form">
        {if $passwd_changed}
            <p>
                {'Password successfully changed.'|i18n( 'ezforgotpassword/generate' )}
            </p>
        {else}
            {if $passwd_not_match}
                <div class="warning">
                    <h2>{'Given passwords do not match. Please re-type and try again.'|i18n( 'ezforgotpassword/generate' )}</h2>
                </div>
            {elseif $passwd_too_short}
                <div class="warning">
                    <h2>{'The password must be at least %1 characters long.'|i18n( 'ezforgotpassword/generate', '', array( ezini( 'UserSettings','MinPasswordLength', 'site.ini' ) ) )}</h2>
                </div>
            {/if}
            <p>
                {'Please fill out following fields to change your password.'}
            </p>
            <form method="POST" action={concat( '/ezforgotpassword/generate/', $hash )|ezurl()}>
                <div class="block">
                    <label>
                        {"New password"|i18n( 'ezforgotpassword/generate' )}:
                        <div class="labelbreak"></div>
                        <input class="halfbox" type="password" name="new_password" />
                    </label>
                </div>
                <div class="block">
                    <label>
                        {"Repeat password"|i18n( 'ezforgotpassword/generate' )}:
                        <div class="labelbreak"></div>
                        <input class="halfbox" type="password" name="repeat_password" />
                    </label>
                </div>
                <div class="buttonblock">
                    <input class="button dark" type="submit" value="{'Change password'|i18n( 'ezforgotpassword/generate' )}" />
                    <span class="button-end dark"></span>
                </div>
            </form>
        {/if}
    </div>
</div>