<?php

    // open database
    $room = $_POST['room'];
    include('config.php');

    // grab all messages from db
    $sql = "SELECT * FROM chats where room = $room";
    $results = $db->query($sql);

    $return_array = array();

    while ($row = $results->fetchArray()) {

        $result_array = array();
        $result_array['id'] = $row['id'];
        $result_array['name'] = $row['name'];

        $result_array['message'] = html_entity_decode(stripslashes($row['message']));

        array_push($return_array, $result_array);

    }

    print json_encode($return_array);


    // package up and send to client


    exit();
 ?>