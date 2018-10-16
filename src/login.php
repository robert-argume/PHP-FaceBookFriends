<?php
    require 'config/config.php';
    
    $helper = $fb->getRedirectLoginHelper();
    if (isset($_GET['state'])) {
        $helper->getPersistentDataHandler()->set('state', $_GET['state']);
    }
    
    //$permissions = ['email, user_friends']; // Optional permissions
    $permissions = ['email']; // Optional permissions
    $loginUrl = $helper->getLoginUrl($config['login_callback_url'], $permissions);
    
    echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';

?>