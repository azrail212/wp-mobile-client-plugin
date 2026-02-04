<?php
function wp_mobile_client_register_routes() {
    register_rest_route('bgh/v1', '/health', array(
        'methods' => 'GET',
        'callback' => 'wp_mobile_client_health_check',
        'permission_callback' => '__return_true'
    ));

     register_rest_route('bgh/v1', '/login', [
        'methods'  => 'POST',
        'callback' => 'bgh_mobile_login',
        'permission_callback' => '__return_true',
    ]);
}

function wp_mobile_client_health_check() {
    return new WP_REST_Response(array(
        'status' => 'ok',
        'timestamp' => current_time('mysql'),
        'version' => '1.0.0'
    ), 200);
}


function bgh_mobile_login(WP_REST_Request $request) {

    $username = $request->get_param('username');
    $password = $request->get_param('password');

    if (empty($username) || empty($password)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Username and password are required',
        ], 400);
    }

    $user = wp_authenticate($username, $password);

    if (is_wp_error($user)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    return new WP_REST_Response([
        'success' => true,
        'user' => [
            'id'       => $user->ID,
            'username' => $user->user_login,
            'email'    => $user->user_email,
            'roles'    => $user->roles,
        ],
    ], 200);
}
