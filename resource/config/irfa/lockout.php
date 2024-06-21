<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Login Attemps
    |--------------------------------------------------------------------------
    | limit failed login, default : 3
    */
    'login_attempts' => env('LOGIN_ATTEMPTS', 3),
    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    | Write log if login fail
    */
    'logging' => env('LOGGING', true),
    /*
    |--------------------------------------------------------------------------
    | Input name
    |--------------------------------------------------------------------------
    | default : email
    */

    'input_name' => "email",
   
    /*
    |--------------------------------------------------------------------------
    | File path
    |--------------------------------------------------------------------------
    | You can change the file location as you wish
    | default storage_path('lockout/account/locked/')
    */

    'lockout_file_path' => storage_path('lockout/account/locked/'),
    /*
    |--------------------------------------------------------------------------
    | Redirect Url
    |--------------------------------------------------------------------------
    | Redirect if account is locked
    | default '/login';
    */

    'redirect_url' => "/login",
    /*
    |--------------------------------------------------------------------------
    | Protected URL Path
    |--------------------------------------------------------------------------
    | Protect your login action url path
    | example: ['login','admin/login']
    | POST method Only
    */
    
    'protected_action_path' => ["login"],
    /*
    |--------------------------------------------------------------------------
    | Protected Middleware Group
    |--------------------------------------------------------------------------
    | Protect your  middleware Group
    | example: ['web','api']
    | POST method Only
    */

    'protected_middleware_group' => ["web"],
    /*
    |--------------------------------------------------------------------------
    | Message Name
    |--------------------------------------------------------------------------
    */
    'message_name' => "message",

    /*
    |--------------------------------------------------------------------------
    | Except Account
    |--------------------------------------------------------------------------
    | If this is filled in, then the account specified here will never be locked
    | example: ['user@domain.com','myaccount','admin321@mail.com']
    | Default:
    | enable_except_account = false
    | except_account = []
    */
    'enable_except_account' => false,
    'except_account' => [],


];
