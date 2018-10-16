<?php
    // Constants to be used in the app
    $config = include('constants.php');
    
    // Make sure we have a session started
    if (!session_id()) {
        session_start();
    }

    // Support from Facebook PHP SDK
    require '../src/lib/Facebook/autoload.php';

    $fb = new Facebook\Facebook([
        'app_id' => $config['app_id'], 
        'app_secret' => $config['app_secret'],
        'default_graph_version' => $config['graph_version'],
        'persistent_data_handler'=>'session'
    ]);
?>