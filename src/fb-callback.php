<?php
    require './config/config.php';
    
    $helper = $fb->getRedirectLoginHelper();
    
    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    
    if (! isset($accessToken)) {
        if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
        }
        exit;
    }
    
    // Logged in
    echo '<h3>Access Token</h3>';
    var_dump($accessToken->getValue());
    
    // The OAuth 2.0 client handler helps us manage access tokens
    $oAuth2Client = $fb->getOAuth2Client();
    
    // Get the access token metadata from /debug_token
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    echo '<h3>Metadata</h3>';
    var_dump($tokenMetadata);
    
    // Validation (these will throw FacebookSDKException's when they fail)
    $tokenMetadata->validateAppId($config['app_id']); // Replace {app-id} with your app id
    // If you know the user ID this access token belongs to, you can validate it here
    //$tokenMetadata->validateUserId('123');
    $tokenMetadata->validateExpiration();
    
    if (! $accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
        exit;
        }
    
        echo '<h3>Long-lived</h3>';
        echo "<pre>";
        //var_dump($accessToken->getValue());   
        var_dump($accessToken);
        echo "</pre>";
    }
    
    // Current user access token is saved in the session
    $_SESSION['fb_access_token'] = (string) $accessToken;

    // If current user is the BASE USER to compare common friends, then save it to the session
    if ($tokenMetadata->getUserId() == $config['compare_base_user_id']) {
        $_SESSION['base_user_token'] = (string) $accessToken;
    }    
    
    // Login process is finished, redirect to next page
    //header('Location: profile.php');
    header('Location: friends.php');
    
?>