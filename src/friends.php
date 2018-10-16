<?php
		 require './config/config.php';
		 echo "ENTER friends.php";
		 // include database and object files
		include_once './config/database.php';
		include_once 'model/facebookuser.php';
		echo "AFTER including database.php";
		// instantiate database 
		$database = new Database();
		$db = $database->getConnection();
		echo "AFTER CONNECTING TO DATABSE";
		// // Get all Facebook users in the App
		// $allUsers = getAllFacebookUsers($db);
		// if( $allUsers!=null){
		// 		showFormatted($facebookUsers_arr);
		// }
		// else{
		// 		echo json_encode(
		// 				array("message" => "No Facebook Users Found.")
		// 		);
		// }

		// Get one specific Facebook user by FB user Id, the BASE USER to compare friends with
		// Value in the SESSION or the REQUEST. Ex: isset($_GET['id']) ? $_GET['id'] : die();
		$baseUser = getFacebookUserByFacebookUserId($db, $config["compare_base_user_id"]);
		if( $baseUser != null){
				showFormatted($baseUser);
		}
		else{
				// tell the user facebook user does not exist
				echo json_encode(array("message" => "Facebook user does not exist."));
		}
	 
		//if (isset($_SESSION['fb_access_token']) && isset($_SESSION['base_user_token'])) {
		if (isset($_SESSION['fb_access_token']) && $baseUser != null) {
			try {
					$pageSize = $config['records_per_page'];
					
					//// Get friend for CURRENT USER, but only the first page
					//$currentUserFriends = getUserFriendsInFirstPage($fb, $_SESSION['fb_access_token'], $pageSize);

					//// Get friend for BASE USER, but only the first page
					//$baseUserFriends = getUserFriendsInFirstPage($fb, $_SESSION['base_user_token'], $pageSize);

					// Get friends for BASE USER by merging all pages and sorting in ascendint order
					//$baseUserToken = $_SESSION['base_user_token'];
					$baseUserToken = $baseUser["accesstoken"];
					echo 'BASE USER: ' . getUser($fb, $baseUserToken)->getName();
					$baseUserFriends = getUserFriends($fb, $baseUserToken, $pageSize);
					usort($baseUserFriends, 'compareId');
					showFormatted($baseUserFriends);

					// Get friends for CURRENT LOGGED IN USER by merging all pages and sorting in ascendint order
					echo 'CURRENT USER: ' . getUser($fb, $_SESSION['fb_access_token'])->getName();
					$currentUserFriends = getUserFriends($fb, $_SESSION['fb_access_token'], $pageSize);
					usort($currentUserFriends, 'compareId');
					showFormatted($currentUserFriends);

					// Find intersection of both friends arrays and show results
					$commonFriends= getIntersectionArray($baseUserFriends, $currentUserFriends);
					echo 'NUMBER OF COMMON FRIENDS = ' . count($commonFriends);
					showFormatted($commonFriends);
						
			} catch( Facebook\Exceptions\FacebookSDKException $e ) {
				echo $e->getMessage();
				exit;
			}
		}
	else{
		echo 'No session information for Base User, or for Current User';
		exit;
	}

	
	// UTILITY PRIVATE FUNCTIONS

	// Uses Graph API to get friends that installed the App. Only first page from response.
	function getUserFriendsInFirstPage( $fb, $token, $pageSize ) {
			$res = $fb->get('/me/friends?limit=' . $pageSize, $token);	
			$res = $res->getGraphEdge()->asArray();
			return $res;
	}

	// Uses Graph API to get friends that installed the App, by merging all pages into a single array
	function getUserFriends( $fb, $token, $pageSize ) {
		$allFriendsArray = array();
		$res = $fb->get('/me/friends?limit=' . $pageSize, $token);	
		$pagesEdge = $res->getGraphEdge();
		
		// Call every page in the response, and merge each page
		do {
				// Get friends from a single page
				$friendsArray = $pagesEdge->asArray();
				//var_dump($friendsArray);
				// Merge a single page into one resulting array.
				$allFriendsArray = array_merge($friendsArray, $allFriendsArray);
		} while ($pagesEdge = $fb->next($pagesEdge));

		return $allFriendsArray;
}

	// Show an object internal data in a readable way
	function showFormatted( $object ) {
			echo "<pre>";
			print_r($object);
			echo "</pre>";
	}

	// Uses Graph API to get User Object from a token
	function getUser( $fb, $token ) {
			try {
				// Returns a `Facebook\FacebookResponse` object
				$response = $fb->get('/me?fields=id,name,email,gender,link,cover,picture', $token);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				echo 'Graph returned an error: ' . $e->getMessage();
				exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit;
			}
			
			// Get user object
			return $response->getGraphUser();
	}

	// Compare function callback for PHP usort() method. Compares two friends
	function compareId($a, $b)
	{
		if ($a["id"] == $b["id"]) {
			return 0;
		}
		
		return ($a["id"] < $b["id"]) ? -1 : 1;
	}

	// Get intersection elements between 2 arrays of friends
	// Arrays $a and $b are required to be sorted in ascending order
	function getIntersectionArray($a, $b){
		$result = array();
		$i = 0;
		$j = 0;
		while ($i < count($a) && $j < count($b)){
				if ($a[$i]["id"] == $b[$j]["id"]){
					array_push($result, $a[$i]);
					$i++;
					$j++;
				}
				else if ($a[$i]["id"] < $b[$j]["id"]){
					$i++;
				}
				else if ($a[$i]["id"] > $b[$j]["id"]){
					$j++;
				}
		}
		return $result;
	}

	function getAllFacebookUsers($db){
		// initialize object
		$facebookUser = new FacebookUser($db);
		
		// query facebookUser READ METHOD
		$stmt = $facebookUser->read();
		$num = $stmt->rowCount();
		
		// check if more than 0 record found
		if($num>0){
		
				// facebookUsers array
				$facebookUsers_arr=array();
				$facebookUsers_arr["records"]=array();
		
				// retrieve our table contents
				// fetch() is faster than fetchAll()
				// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
						// extract row data into local variables $
						extract($row);
		
						$facebookUser_item=array(
								"id" => $id,
								"userid" => $userid,
								"accesstoken" => $accesstoken,
								"name" => $name
						);
		
						array_push($facebookUsers_arr["records"], $facebookUser_item);
				}
				return $facebookUsers_arr;
		}
		else{
			return null;
		}
	}

	function getFacebookUserByFacebookUserId($db, $userid){
		//Query FacebookUsers READ ONE METHOD
		//prepare product object
		$facebookUser = new FacebookUser($db);
		
		// set USERID property of record with the BASE USER ID
		//isset($_GET['id']) ? $_GET['id'] : die();
		$facebookUser->userid = $userid;
		
		// read the details of product to be edited
		$facebookUser->readOne();
		
		if($facebookUser->name!=null){
				// create array
				$facebookUser_arr = array(
						"id" =>  $facebookUser->id,
						"name" => $facebookUser->name,
						"userid" => $facebookUser->userid,
						"accesstoken" => $facebookUser->accesstoken
				);
				return $facebookUser_arr;
		}
		else{
			return null;
		}
	}

?>