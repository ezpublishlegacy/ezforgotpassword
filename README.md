ezforgotpassword 1.0.0
======================

Description
-----------
This is an exclusive eZPublish extension, which comes with different way of
restoring the forgotten password. Instead of sending the password by email,
a message contains the unique URL that redirects to page where user can generate
a new password.

Installation
------------
1. Get the extension. Go to your `extension` folder within your eZ installation, then run:
`git clone git@github.com:makingwaves/ezforgotpassword.git`

2. Enable extension adding entry to site.ini.append.php:
`[ExtensionSettings]
ActiveExtensions[]=ezforgotpassword`

3. Go to *User accounts => Roles and policies* and click on Anonymous group.
Now click "Edit" button and add "New policy". As a module select *ezforgotpassword*
and as a function pick *generate*. Then click *Grant full access*.

4. Now you need to include PHP class, so go to the root directory of your eZ installation and run:
`php bin/php/ezpgenerateautoloads.php`