<?php
/**
 * Tests the REST API in phpList with common functions
 * The test is using a client rest approach with the Snoopy-object-class
 * Creating list, user, message
 * And cleaning up after
 */

include 'phplist_restapi_helper.php';

$plugin = $GLOBALS["plugins"][$_GET["pi"]];

if ( isset( $_POST['restapi_test_login'] ) ){
	$login = $_POST['restapi_test_login'];
	$password = $_POST['restapi_test_password'];
	if ( !empty($login) && !empty($password) ){
		Sql_Query( sprintf( "UPDATE %s SET value='%s' WHERE item='restapi_test_login'", $tables["config"], $login ) );
		Sql_Query( sprintf( "UPDATE %s SET value='%s' WHERE item='restapi_test_password'", $tables["config"], $password ) );
	}
}

$url = apiUrl( $website );

$login = getConfig('restapi_test_login');
$password = getConfig('restapi_test_password');

if (empty($login)) {
  print Error('Please configure the login details in the settings page<br/>Parameters: <strong>restapi_test_login</strong> and <strong>restapi_test_password</strong> for admin login.');
	?>
		<form method="POST">
			<input type="text" placeholder="restapi_test_login" name="restapi_test_login" /><br/>
			<input type="password" placeholder="restapi_test_password" name="restapi_test_password" /><br/>
			<input type="submit" value="Save" />
		</form>
	<?php

  return;
}


$step = 1;

?>

<html>
    <h1>RESTAPI TEST</h1>

    <h2>Step <?php echo $step++; ?> - Login</h2>

    <?php

    //Get the loginname and password!
    $id = $_SESSION["logindetails"]["id"];

    $api = new phpList_RESTAPI_Helper( $url );
    $result = $api->login( $login, $password );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    ?>


    <h2>Step <?php echo $step++; ?> - Count existing lists</h2>
    <?php

    $result = $api->listsGet();
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    //Present the lists fetched
    echo "Number of lists is " . count($result->data);
    foreach($result->data as $key => $list){
        if ( $key == 0 ) echo ' ( ';
        if ( $key > 0 && $key < count( $result->data ) ) echo ' , ';
        echo $list->name;
        if ( $key > 0 && $key == count( $result->data ) - 1 ) echo ' ) ';
    }

    ?>

    <h2>Step <?php echo $step++; ?> - Create a new list</h2>
    <?php

    $result = $api->listAdd( 'Testlist', 'This is just a test');
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    $list_id = $result->data->id;
    echo 'The new list ID = ' . $result->data->id;

    ?>

    <h2>Step <?php echo $step++; ?> - Change the name of the list</h2>
    <?php

    $result = $api->listUpdate( $list_id, 'Testlist ' . date('Y-m-d H:i:s'));
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'The new list has a new name: ' . $result->data->name;

    ?>

    <h2>Step <?php echo $step++; ?> - Count users / subscribers</h2>
    <?php

    $result = $api->usersGet();
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    //Present the lists fetched
    echo "Total number of users: " . count($result->data);

    ?>

    <h2>Step <?php echo $step++; ?> - Check if your admin address is in users</h2>
    <?php

		$admin_address = getConfig("admin_address");

    $result = $api->userGetByEmail( $admin_address );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    $user_id = $result->data->id;

    if (!$user_id){
        //Add admin email to users
        $result = $api->userAdd( $admin_address, 1, 1, '#PasswordNotSet#' );
        if ($result->status != 'success'){
            echo $result->data->message;
            return;
        }
        $user_id = $result->data->id;
        echo "Admin Email (" . $admin_address . ") is in Users now with id = " . $user_id . '<br/>';
    }

    echo "Added a new User with the Admin Email Address, id = " . $user_id;

    ?>

    <h2>Step <?php echo $step++; ?> - Change user password</h2>
    <?php

    $result = $api->userUpdate( $user_id, $admin_address, 1, 1, '#NewPassword#' );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo '"The Admin as a User" password has changed!';

    ?>

    <h2>Step <?php echo $step++; ?> - Add User to list</h2>
    <?php

    $result = $api->listUserAdd( $list_id, $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'The User is assigned to the new list created. ';

    ?>

		<h2>Step <?php echo $step++; ?> - Count users / subscribers AGAIN!</h2>
		<?php

		$result = $api->usersGet();
		if ($result->status != 'success'){
			echo $result->data->message;
			return;
		}

		//Present the lists fetched
		echo "Total number of users: " . count($result->data);

		?>

    <h2>Step <?php echo $step++; ?> - Lists where the user is assigned</h2>
    <?php

    $result = $api->listsUser( $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    foreach($result->data as $key => $list){
        if ( $key > 0 && $key < count( $result->data ) ) echo ' , ';
        echo $list->name;
    }

    ?>

    <h2>Step <?php echo $step++; ?> - Check if template for test exists (create)</h2>
    <?php

    $result = $api->templateGetByTitle( 'API Test' );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    $template_id = $result->data->id;

    if ( !$template_id ){

        $template_content = file_get_contents(__DIR__ . '/template.html');
        $result = $api->templateAdd( 'API Test', $template_content );
        if ($result->status != 'success'){
            echo $result->data->message;
            return;
        }
        echo 'New template created for test.<br/>';
        $template_id = $result->data->id;
    }

    $result = $api->templateGet( $template_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'Title for template (ID=' . $result->data->id . '): ' . $result->data->title;

    ?>

    <h2>Step <?php echo $step++; ?> - Update template title</h2>
    <?php

    $template_content = file_get_contents(__DIR__ . '/template.html');
    $result = $api->templateUpdate( $template_id, 'API Test ' . date('Y-m-d H:i:s'), $template_content );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Title for template (ID=' . $result->data->id . '): ' . $result->data->title;

    ?>

    <h2>Step <?php echo $step++; ?> - List all templates</h2>
    <?php

    $result = $api->templatesGet();
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    foreach($result->data as $key => $template){
        if ( $key > 0 && $key < count( $result->data ) ) echo ' , ';
        echo $template->title;
    }

    ?>

    <h2>Step <?php echo $step++; ?> - Create a message</h2>
    <?php

    $result = $api->messageAdd( 'Test API from plugin', $admin_address, $admin_address, 'TEST API', 'TEST API', $template_id, date('Y-m-d H:i:s'));
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    $message_id = $result->data->id;
    echo 'Message created (ID=' . $message_id . ').';

    ?>

    <h2>Step <?php echo $step++; ?> - Update message</h2>
    <?php

    $result = $api->messageUpdate( $message_id, 'Test API from plugin updated ' .date('Y-m-d H:i:s'), $admin_address, $admin_address, 'TEST API', 'TEST API', $template_id, date('Y-m-d H:i:s'), 'submitted', $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Message updated and submitted (ID=' . $result->data->id . ').';

    ?>

    <h2>Step <?php echo $step++; ?> - Messages count</h2>
    <?php

    $result = $api->messagesGet();
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Messages in phpList: ' . count($result->data) . '.';

    ?>

    <h2>Step <?php echo $step++; ?> - Assign message to list</h2>
    <?php

    $result = $api->listMessageAdd( $list_id, $message_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Message assigned to the created list.';
    ?>

    <h2>Step <?php echo $step++; ?> - Process Queue</h2>
    <?php

    $result = $api->processQueue( $login, $password, true );

    sleep(5);

    ?>

    <h2>Step <?php echo $step++; ?> - Unassign user from list</h2>
    <?php

    $result = $api->listUserDelete( $list_id, $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo $result->data . '.';

    ?>

    <h2>Step <?php echo $step++; ?> - Delete the user</h2>
    <?php

    $result = $api->userDelete( $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'Admin deleted from Users.';

    ?>

    <h2>Step <?php echo $step++; ?> - Delete the list</h2>
    <?php

    $api->listDelete( $list_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'The new list is now deleted';

    ?>

    <h2>Step <?php echo $step++; ?> - Delete template</h2>
    <?php

    $api->templateDelete( $template_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'The template is now deleted';

    ?>

		<h2>The test completed successfully!</h2>

</html>

<?php

    function apiUrl( $website ){

        $url = '';
        if( !empty( $_SERVER["HTTPS"] ) ){
            if($_SERVER["HTTPS"]!=="off")
                $url = 'https://'; //https
            else
                $url = 'http://'; //http
        }
        else
            $url = 'http://'; //http

        $api_url = str_replace( 'page=main&pi=restapi_test', 'page=call&pi=restapi', $_SERVER['REQUEST_URI'] );
        $api_url = str_replace( 'page=main&pi=restapi', 'page=call&pi=restapi', $api_url );

        $url = $url . $website . $api_url;
        $url = rtrim($url,'/');

        return $url;

    }

?>
