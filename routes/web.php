<?php
/**
 * Route Definitions
 */

return [
    '/' => ['handler' => 'RequestController@landing', 'middleware' => []],
    '/login' => ['handler' => 'AuthController@login', 'middleware' => ['csrf']],
    '/logout' => ['handler' => 'AuthController@logout', 'middleware' => []],

    // Admin routes
    '/admin/dashboard' => ['handler' => 'AdminController@dashboard', 'middleware' => ['admin_only']],
    '/admin/requests' => ['handler' => 'AdminController@requests', 'middleware' => ['admin_only']],
    '/admin/requests/view' => ['handler' => 'AdminController@viewRequest', 'middleware' => ['admin_only']],
    '/admin/programs' => ['handler' => 'AdminController@programs', 'middleware' => ['admin_only']],
    '/admin/programs/create' => ['handler' => 'AdminController@createProgram', 'middleware' => ['admin_only', 'csrf']],
    '/admin/programs/edit' => ['handler' => 'AdminController@editProgram', 'middleware' => ['admin_only']],
    '/admin/programs/update' => ['handler' => 'AdminController@updateProgram', 'middleware' => ['admin_only', 'csrf']],
    '/admin/programs/delete' => ['handler' => 'AdminController@deleteProgram', 'middleware' => ['admin_only', 'csrf']],
    '/admin/system-status' => ['handler' => 'AdminController@systemStatus', 'middleware' => ['admin_only', 'csrf']],
    '/admin/reports' => ['handler' => 'AdminController@reports', 'middleware' => ['admin_only']],
    '/admin/reports/export' => ['handler' => 'AdminController@exportReport', 'middleware' => ['admin_only']],
    '/admin/export/csv' => ['handler' => 'AdminController@exportCSV', 'middleware' => ['admin_only']],
    '/admin/export/logs' => ['handler' => 'AdminController@exportLogsCSV', 'middleware' => ['admin_only']],
    '/admin/profile' => ['handler' => 'AdminController@profile', 'middleware' => ['admin_only']],
    '/admin/profile/update' => ['handler' => 'AdminController@updateProfile', 'middleware' => ['admin_only', 'csrf']],

    // Client routes
    '/client' => ['handler' => 'RequestController@landing', 'middleware' => []],
    '/client/track' => ['handler' => 'RequestController@track', 'middleware' => []],
    '/client/submit' => ['handler' => 'RequestController@submit', 'middleware' => ['csrf']],
    '/client/educational' => ['handler' => 'RequestController@educational', 'middleware' => []],
    '/client/medical' => ['handler' => 'RequestController@medical', 'middleware' => []],
    '/client/burial' => ['handler' => 'RequestController@burial', 'middleware' => []],
    '/client/employment' => ['handler' => 'RequestController@employment', 'middleware' => []],
    '/client/transportation' => ['handler' => 'RequestController@transportation', 'middleware' => []],
    '/client/proof' => ['handler' => 'RequestController@proof', 'middleware' => []],
];
