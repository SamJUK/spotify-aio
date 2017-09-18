<div id='control' class='page'>
    <div class='ctrls'>
        <a href='#' onclick='PreviousTrack()'><button class='btn'>Previous Track</button></a>
        <a href='#' onclick='PlayPause()'><button class='btn'>Play / Pause</button></a>
        <a href='#' onclick='NextTrack()'><button class='btn'>Next Track</button></a>
        <a href='index.php'><button class='btn'>Back</button></a>
    </div>
    <div class='info'>
        <div class='artwork'>
            <div id='album_artwork' class='container'>
                <i id='album_icon' class="material-icons">album</i>
            </div>
        </div>
        <div class='text'>
            <h1 id='track_name'></h1>
            <h2 id='artist_name'></h2>
        </div>
        <div class='states'>
            <div class='container'>
                <i id='shuffle_icon'class="material-icons" onclick='Shuffle()'>shuffle</i>
                <i id='repeat_icon' class="material-icons" onclick='Repeat()'>repeat</i>
                <!--repeat_one-->
            </div>
        </div>
    </div>
</div>

<script> 
    var rs = 'off';
    function updateTrackInfo(){
        ajax('gettrackinfo', json => {
            var j = JSON.parse(json);

            // Artwork
            document.getElementById('album_artwork').style.backgroundImage = `url(${j.item.album.images[0].url})`;
            document.getElementById('album_artwork').className = 'container no-icon';

            // Track Name
            document.getElementById('track_name').innerText = j.item.name;

            // Artist
            document.getElementById('artist_name').innerText = j.item.artists[0].name;

            // Update Shuffle Repeat
            updateplayerState();
        });
    };
    function updateplayerState(){
        ajax('getplayerstate', json => {
            var j = JSON.parse(json);
            var shuffleState = j.shuffle_state;
            var repeatState = j.repeat_state;

            document.getElementById('shuffle_icon').className = (shuffleState) ? 'material-icons active' : 'material-icons';

            if ( repeatState == 'context' ){
                document.getElementById('repeat_icon').className = 'material-icons active';
                document.getElementById('repeat_icon').innerText = 'repeat';
                rs = 'context';
            }
            else if ( repeatState == 'track'){
                document.getElementById('repeat_icon').className = 'material-icons active';
                document.getElementById('repeat_icon').innerText = 'repeat_one';
                rs = 'track';
            }
            else{
                document.getElementById('repeat_icon').className = 'material-icons';
                document.getElementById('repeat_icon').innerText = 'repeat';
                rs = 'off';
            };

        });
    };
    function PlayPause(){
        ajax('playpause');
    };
    function PreviousTrack(){
        ajax('previous');
    };
    function NextTrack(){
        ajax('next');
    };
    function Shuffle(){
        ajax('shuffle');
        var a = document.getElementById('shuffle_icon').className == 'material-icons';
        document.getElementById('shuffle_icon').className = (a) ? 'material-icons active' : 'material-icons';
    };
    function Repeat(){
        ajax('repeat');

        if ( rs == 'off'){
            document.getElementById('repeat_icon').className = 'material-icons active';
            document.getElementById('repeat_icon').innerText = 'repeat';
            rs = 'context';
        }else if ( rs == 'context'){
            document.getElementById('repeat_icon').className = 'material-icons active';
            document.getElementById('repeat_icon').innerText = 'repeat_one';
            rs = 'track';
        }else{
            document.getElementById('repeat_icon').className = 'material-icons';
            document.getElementById('repeat_icon').innerText = 'repeat';
            rs = 'off';
        }; 

    };
    function ajax(endpoint, callback){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                callback(this.responseText);
            };
        };
        xhttp.open("GET", 'api.php?e=' + endpoint, true);
        xhttp.send();
    };

    setInterval(() => {
        updateTrackInfo();
    }, 5000);
    
    updateTrackInfo();
    
</script>