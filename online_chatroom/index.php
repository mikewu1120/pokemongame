<!doctype html>
<html>
    <head>
        <title>Let's Chat</title>

        <!-- bring in the jQuery library -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        <!-- custom styles -->
        <style>
        #chat_log {
            display: block;
            width: 500px;
            height: 300px;
        }
        .hidden {
            display: none;
        }
        .error{
            text-align: center;
            height: 25px;
            width: 200px;
            background-color: red;
            color: white;
        }
        </style>
    </head>
    <body>
        <h1>Let's Chat</h1> 

        <div>
            <button id="room1">Room 1</button>
            <button id="room2">Room 2</button>
            <button id='room3'>Room 3</button>
        </div>

        <div id="panel_name">
            <div id="invalid_un" class="error hidden">username is invalid</div>    
            Name: <input type="text" id="username">
            <button id="button_save">Let's Chat!</button>
        </div>

        <div id="panel_chat" class="hidden">
            <div id="invalid_change" class="error hidden">username is invalid</div>
            <input type="text" id="name_changed">
            <button id="change_name">Change Name</button>
            <textarea readonly id="chat_log"></textarea>
            <div id="too_short" class="error hidden">message too short</div>
            <div id="bad_word" class="error hidden">banned word contains!</div>
            <input type="text" id="message">
            <button id="button_send">Send Message</button>
        </div>



        <script>
        let selectedName;
        let selectedRoom = 1; 
        let getDataInterval;
        $(document).ready(function() {


            // DOM refs
            let panel_name = document.getElementById('panel_name');
            let username = document.getElementById('username');
            let button_save = document.getElementById('button_save');
            let panel_chat = document.getElementById('panel_chat');
            let chat_log = document.getElementById('chat_log');
            let message = document.getElementById('message');
            let button_send = document.getElementById('button_send');
            let too_short = document.getElementById('too_short');
            let invalid_un = document.getElementById('invalid_un');
            let name_changed = document.getElementById('name_changed');
            let change_name = document.getElementById('change_name');
            let invalid_change = document.getElementById('invalid_change');
            let room1 = document.getElementById('room1');
            let room2 = document.getElementById('room2');
            let room3 = document.getElementById('room3');
            let bad_word = document.getElementById('bad_word');

            room1.style.backgroundColor='yellow';

            room1.addEventListener('click', function(){
                room1.style.backgroundColor='yellow';
                room2.style.backgroundColor=null;
                room3.style.backgroundColor=null;
                selectedRoom=1;
                clearInterval(getDataInterval);
                getData();
                getDataInterval=null;
                getDataInterval= setInterval(getData,2000);
            })
            room2.addEventListener('click', function(){
                room2.style.backgroundColor='yellow';
                room1.style.backgroundColor=null;
                room3.style.backgroundColor=null;
                selectedRoom=2;
                clearInterval(getDataInterval);
                getData();
                getDataInterval=null;
                getDataInterval= setInterval(getData,2000);
            })
            room3.addEventListener('click', function(){
                room3.style.backgroundColor='yellow';
                room1.style.backgroundColor=null;
                room2.style.backgroundColor=null;
                selectedRoom=3;
                clearInterval(getDataInterval);
                getData();
                getDataInterval=null;
                getDataInterval= setInterval(getData,2000);
            })

            $.ajax({
                url: 'has_logged_in.php',
                success: function(data,status){
                    if(data){
                        document.querySelector('h1').innerHTML="Let's Chat - "+data;
                        panel_name.classList.add('hidden');
                        panel_chat.classList.remove('hidden');
                        selectedName=data;
                    }
                }
            })

            button_save.addEventListener('click', function() {

                // validate the user's name using an AJAX call to the server
                $.ajax({
                    url: 'validate_name.php',
                    type: 'post',
                    data: {
                        name: username.value
                    },
                    success: function(data, status) {
                        if (data == 'valid') {
                            document.querySelector('h1').innerHTML="Let's Chat - "+username.value;
                            invalid_un.classList.add('hidden');
                            selectedName = username.value;
                            panel_name.classList.add('hidden');
                            panel_chat.classList.remove('hidden');
                            document.cookie = 'name='+selectedName;
                        }
                        else{
                            invalid_un.classList.remove('hidden');
                            username.value='';  
                        }
                    }
                });

                // if valid, hide the panel_name panel and show the
                // panel_chat panel
            })

            change_name.addEventListener('click', function(){
                $.ajax({
                    url: 'validate_name.php',
                    type: 'post',
                    data: {
                        name: name_changed.value
                    },
                    success: function(data, status){
                        if (data == 'valid') {
                            document.querySelector('h1').innerHTML="Let's Chat - "+name_changed.value;
                            invalid_change.classList.add('hidden');
                            selectedName = name_changed.value;  
                            name_changed.value='';
                            document.cookie = 'name='+selectedName;  
                            alert('You have changed your name!')
                        }
                        else{
                            invalid_change.classList.remove('hidden');
                            name_changed.value='';  
                        }
                    }
                })
            })

            button_send.addEventListener('click', function() {
                // make an ajax call to the server to save the message
                $.ajax({
                    url: 'save_message.php',
                    type: 'post',
                    data: {
                        name: selectedName,
                        message: message.value,
                        room: selectedRoom
                    },
                    success: function(data, status) {
                        if(data == 'success'){
                            too_short.classList.add('hidden');
                            bad_word.classList.add('hidden');
                            chat_log.value += selectedName + ': ' + message.value + "\n"; 
                            message.value='';
                        }      
                        if(data == 'fail'){
                            too_short.classList.remove('hidden');
                        }
                        if(data == 'bad'){
                            message.value='';
                            bad_word.classList.remove('hidden');
                        }
                    }
                });

            // when it's successful we should add the message to
            // the chat log so we can see it
            });

            let isOn=false;
            chat_log.onmouseover = function(e){
                isOn=true;
            }
            chat_log.onmouseout = function(e){
                isOn=false;
            }
            
            function getData() {

            $.ajax({
                url: 'get_messages.php',
                type: 'post',
                data:{
                    room: selectedRoom
                },
                success: function(data, status) {
                    let parsed = JSON.parse(data);

                    let newChatroom = '';
                    for (let i = 0; i < parsed.length; i++) {
                        newChatroom += parsed[i].name + ': ' + parsed[i].message + "\n";
                    }
                    chat_log.value = newChatroom;
                    if(!isOn){
                        chat_log.scrollTop = chat_log.scrollHeight;
                    }
                    else{
                        chat_log.scrollTop;
                    }

                    
                }
            })

            }

            getData();
            getDataInterval= setInterval(getData,2000);


        });









        </script>

    </body>
</html>
