{set-block scope=root variable=subject}{'Reset your password'|i18n( 'ezforgotpassword/mail' )}{/set-block}

<p>
    {'Click on following link to generate new password:'|i18n( 'ezforgotpassword/mail' )}
</p>
<p>
    <a href={$link|ezurl( 'no', 'full' )}>
        {$link|ezurl( 'no', 'full' )}
    </a>
</p>