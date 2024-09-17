<?php

    // open database
    include('config.php');

    // get post variables
    $name = $_POST['name'];
    $message = $_POST['message'];
    $room = $_POST['room'];

    $sql = "SELECT * FROM banned_word";
    $results = $db->query($sql);

    $bad_words = array();

    while ($row = $results->fetchArray()) {
        array_push($bad_words, $row['word']);
    }

    // make sure there's a message here
    if (strlen($message) > 0) {
        $hasBad=false;
        for($i=0;$i<sizeof($bad_words);$i++){
            if(strpos($message,$bad_words[$i])!==false){
                $hasBad=true;
            }
        }

        if($hasBad){
            print 'bad';
            exit();
        }
        else{
            // add to database
            $message = $db->escapeString(addslashes(htmlspecialchars($message)));

            $sql = "INSERT INTO chats (name, message, room) VALUES ('$name', '$message', $room)";
            $db->query($sql);

            print "success";
            exit();
        }
    }

    print "fail";
    exit();


 ?>




