<?php

/*
  | -------------------------------------------------------------------------
  | Authentication options.
  | -------------------------------------------------------------------------
 */

$config['admin_email'] = "danny.nunez15@gmail.com";                 // Admin Email, admin@example.com
$config['default_group'] = 'users';                                    // Default group, use name
$config['admin_group'] = 'admin';                                          // Default administrators group, use name
$config['identity'] = 'email';                                                     // A database column which is used to login with
$config['min_password_length'] = 8;                                     // Minimum Required Length of Password
$config['max_password_length'] = 25;                                  // Maximum Allowed Length of Password
$config['email_activation'] = TRUE;                                     // Email Activation for registration
$config['manual_activation'] = FALSE;                                  // Manual Activation for registration
$config['remember_users'] = TRUE;                                     // Allow users to be remembered and enable auto-login
$config['user_expire'] = 86500;                                             // How long to remember the user (seconds). Set to zero for no expiration
$config['user_extend_on_login'] = FALSE;                           // Extend the users cookies everytime they auto-login
$config['track_login_attempts'] = FALSE;                              // Track the number of failed login attempts for each user or ip.
$config['maximum_login_attempts'] = 3;                                // The maximum number of failed login attempts.
$config['forgot_password_expiration'] = 0;                            // The number of seconds after which a forgot password request will expire. If set to 0, forgot password requests will not expire.
$config['registration_status'] = true;                                        // true or false - Allow registration 
$config['user_login'] = true;                                                    // true or false - Allow logins

/*
  | -------------------------------------------------------------------------
  | Error Messages
  | -------------------------------------------------------------------------
 */

$config['user_reg_error_message'] = '<div class="alert"><p>There was an error in processing your registration. Please try again.</p></div>';
$config['user_login_error_message'] = '<div class="alert"><p>The credentials entered were incorrect.</p></div>';

/*
  | -------------------------------------------------------------------------
  | Tool Tips Messages
  | -------------------------------------------------------------------------
 */

$config['password_tooltip'] = '<p>'
    . '<strong>Minimum requirements</strong>:<br>'
    . 'Password Length minimum: 8<br>'
    . 'Uppercase letters minimum: 2<br>'
    . 'Lowercase letters minimum: 2<br>'
    . 'Numbers minimum: 2<br>'
    . '</p>';

$config['name_tooltip'] = '<p>Alphabetical characters only.</p>';

$config['birthday_tooltip'] =  '';

/*
  | -------------------------------------------------------------------------
  | Permission Messages
  | -------------------------------------------------------------------------
 */


/*
  | -------------------------------------------------------------------------
  | Login/Logout Messages
  | -------------------------------------------------------------------------
 */
$config['logout_message'] =  array('message' => '<p>You have successfully logged out of your account.</p>');
$config['registration_message'] =  "<p>You have successfully registered. Create a registry.</p>";

/*
  | -------------------------------------------------------------------------
  | Profile Messages
  | -------------------------------------------------------------------------
 */

$config['profile_update_successful'] =  array('message' => '<p>Your profile has been updated.</p>');
$config['profile_update_unsuccessful'] =  array('message' => '<p>Sorry we were unable to update your profile.</p>');

/*
  | -------------------------------------------------------------------------
  | Permission Messages
  | -------------------------------------------------------------------------
 */

$config['add_permission_successful'] =  array('message' => '<p>New permission successfully added.</p>');
$config['add_permission_error'] =  array('message' => '<p>Sorry we were unable to add the permission.Please try again.</p>');

