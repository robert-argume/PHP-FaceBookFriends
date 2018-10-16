<?php
    require './config/config.php';
    
    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->get('/me?fields=id,name,email,gender,link,cover,picture', $_SESSION['fb_access_token']);
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
      }
      
      $user = $response->getGraphUser();
      echo "<pre>";
      print_r($user);
      echo 'Name: ' . $user['name'];
      // OR
      // echo 'Name: ' . $user->getName();
      echo "</pre>";
      
      echo "<pre>";
      echo 'AccessToken: ' . $_SESSION['fb_access_token'];
      echo "</pre>";
      
?>