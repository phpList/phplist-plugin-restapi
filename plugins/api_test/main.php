<?php
/**
 * Tests the REST API in phpList with common functions
 * The test is using a client rest approach with the Snoopy-object-class
 * Creating list, user, message
 * And cleaning up after
 */

include 'phplist_api_helper.php';

$plugin = $GLOBALS["plugins"][$_GET["pi"]];

$url = apiUrl( $website );

?>

<html>
    <h1>API TEST</h1>

    <h2>Step 1 - Login</h2>

    <?php

    //Get the loginname and password!
    $id = $_SESSION["logindetails"]["id"];

    $dbhost = $GLOBALS['database_host'];
    $dbuser = $GLOBALS['database_user'];
    $dbpass = $GLOBALS['database_password'];
    $dbname = $GLOBALS['database_name'];
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "admin WHERE id=:id;";
    $stmt = $db->prepare($sql);
    $stmt->execute( array( ':id' => $id ) );
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $admin = $result[0];

    $api = new phpList_API_Helper( $url );
    $result = $api->login( $admin->loginname, $admin->password );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    ?>


    <h2>Step 2 - Count existing lists</h2>
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

    <h2>Step 3 - Create a new list</h2>
    <?php

    $result = $api->listAdd( 'Testlist', 'This is just a test');
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    $list_id = $result->data->id;
    echo 'The new list ID = ' . $result->data->id;

    ?>

    <h2>Step 4 - Change the name of the list</h2>
    <?php

    $result = $api->listUpdate( $list_id, 'Testlist ' . date('Y-m-d H:i:s'));
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'The new list has a new name: ' . $result->data->name;

    ?>

    <h2>Step 5 - Count users / subscribers</h2>
    <?php

    $result = $api->usersGet();
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    //Present the lists fetched
    echo "Total number of users: " . count($result->data);

    ?>

    <h2>Step 6 - Check if your admin address is in users</h2>
    <?php

    $result = $api->userGetByEmail( $admin->email );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    $user_id = $result->data->id;

    if (!$user_id){
        //Add admin email to users
        $result = $api->userAdd( $admin->email, 1, 1, '#PasswordNotSet#' );
        if ($result->status != 'success'){
            echo $result->data->message;
            return;
        }
        $user_id = $result->data->id;
        echo "Admin Email is in Users now with id = " . $user_id . '<br/>';
    }

    echo "Admin Email id = " . $user_id;

    ?>

    <h2>Step 7 - Change user password</h2>
    <?php

    $result = $api->userUpdate( $user_id, $admin->email, 1, 1, '#NewPassword#' );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'The Admin User password is changed!';

    ?>

    <h2>Step 8 - Add User to list</h2>
    <?php

    $result = $api->listUserAdd( $list_id, $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'The Admin User is assigned to the new list created. ';

    ?>

    <h2>Step 9 - Lists where the user is assigned</h2>
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

    <h2>Step 10 - Check if template for test exists (create)</h2>
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

    <h2>Step 11 - Update template title</h2>
    <?php

    $template_content = file_get_contents(__DIR__ . '/template.html');
    $result = $api->templateUpdate( $template_id, 'API Test ' . date('Y-m-d H:i:s'), $template_content );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Title for template (ID=' . $result->data->id . '): ' . $result->data->title;

    ?>

    <h2>Step 12 - List all templates</h2>
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

    <h2>Step 13 - Create a message</h2>
    <?php

    $result = $api->messageAdd( 'Test API from plugin', $admin->email, $admin->email, 'TEST API', 'TEST API', $template_id, date('Y-m-d H:i:s'));
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    $message_id = $result->data->id;
    echo 'Message created (ID=' . $message_id . ').';

    ?>

    <h2>Step 14 - Update message</h2>
    <?php

    $result = $api->messageUpdate( $message_id, 'Test API from plugin updated ' .date('Y-m-d H:i:s'), $admin->email, $admin->email, 'TEST API', 'TEST API', $template_id, date('Y-m-d H:i:s'), 'submitted', $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Message updated and submitted (ID=' . $result->data->id . ').';

    ?>

    <h2>Step 15 - Messages count</h2>
    <?php

    $result = $api->messagesGet();
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Messages in phpList: ' . count($result->data) . '.';

    ?>

    <h2>Step 16 - Assign message to list</h2>
    <?php

    $result = $api->listMessageAdd( $list_id, $message_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'Message assigned to the created list.';
    ?>

    <h2>Step 17 - Process Queue</h2>
    <?php

    $result = $api->processQueue( $admin->loginname, $admin->password, true );

    ?>

    <h2>Step 18 - Unassign user from list</h2>
    <?php

    $result = $api->listUserDelete( $list_id, $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo $result->data . '.';

    ?>

    <h2>Step 19 - Delete the user</h2>
    <?php

    $result = $api->userDelete( $user_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }

    echo 'Admin deleted from Users.';

    ?>

    <h2>Step 20 - Delete the list</h2>
    <?php

    $api->listDelete( $list_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'The new list is now deleted';

    ?>

    <h2>Step 21 - Delete template</h2>
    <?php

    $api->templateDelete( $template_id );
    if ($result->status != 'success'){
        echo $result->data->message;
        return;
    }
    echo 'The template is now deleted';

    ?>

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

        $api_url = str_replace( 'page=main&pi=api_test', 'page=call&pi=api', $_SERVER['REQUEST_URI'] );
        $api_url = str_replace( 'page=main&pi=api', 'page=call&pi=api', $api_url );

        $url = $url . $website . $api_url;
        $url = rtrim($url,'/');

        return $url;

    }

?>