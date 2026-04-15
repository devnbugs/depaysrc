<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /*
     * ---------------------------------------------------------------
     * Base Url
     * ---------------------------------------------------------------
     *
     * The Gecharl base url upon which others is based, if not set it's going to use the sandbox version
     */
    'base_url' => env('GECHARL_CONNECT_BASE_URL', 'https://www.gecharl.com/api'),


    /*
     * ---------------------------------------------------------------
     * API KEY
     * ---------------------------------------------------------------
     *
     * Your Gecharl API Key
     */
    'api_key' => env('GECHARL_CONNECT_API_KEY'),

    /*
     * ---------------------------------------------------------------
     * Username (Email)
     * ---------------------------------------------------------------
     *
     * Your Gecharl username
     */
    'username' => env('GECHARL_CONNECT_USERNAME'),

];
