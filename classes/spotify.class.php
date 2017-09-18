<?php 

class Spotify {

    static $authorisation_redirect_uri = 'http://192.168.0.16/spotify/callback/';

    static function logout(){
        session_destroy();
        session_start();
    }

    static function get_authorise_url(){
        $base = 'https://accounts.spotify.com/authorize/?';
        $response_type = 'response_type=code';

        // Client ID
        $client_id = file_get_contents('credentials/id.token');
        $client_id = 'client_id='. $client_id;
        
        // Callback URL
        $redirect_uri = 'redirect_uri=' . self::$authorisation_redirect_uri;
        
        // Scopes
        $scopesArray = [ 'user-read-private', 'user-read-email', 'user-read-playback-state', 'user-modify-playback-state'];
        $scopes_string = join(' ', $scopesArray);
        $scope = 'scope=' . $scopes_string;

            // Return Compiled URL
            $url_parts = [$base, $client_id, $response_type, $redirect_uri, $scope];
            return join('&', $url_parts);             
    }

    static function get_access_token($code) {

        $url = 'https://accounts.spotify.com/api/token';

        // POST Parts
        $post_parts = array (
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => self::$authorisation_redirect_uri
        );

        // Authorisation Header
        $client_id = file_get_contents('../credentials/id.token');
        $client_secret = file_get_contents('../credentials/secret.token');
        $Authorisation_Header = base64_encode( $client_id . ':' . $client_secret ); 

        // Make cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        // To prevent MITM attacks comment out the following 2 lines
        // And setup your PHP's cURL certificate information properly
        //
        // https://stackoverflow.com/a/14064903
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                http_build_query($post_parts));

       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic ' . $Authorisation_Header
            ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        return $server_output;
    }

    static function refresh_token(){
        $url = 'https://accounts.spotify.com/api/token';

        // POST Parts
        $post_parts = array (
            'grant_type' => 'refresh_token',
            'refresh_token ' => $_SESSION['refresh_token']
        );
        
        // Authorisation Header
        $client_id = file_get_contents('../credentials/id.token');
        $client_secret = file_get_contents('../credentials/secret.token');
        $Authorisation_Header = base64_encode( $client_id . ':' . $client_secret ); 

        // Make cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        // To prevent MITM attacks comment out the following 2 lines
        // And setup your PHP's cURL certificate information properly
        //
        // https://stackoverflow.com/a/14064903
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, 
                http_build_query($post_parts));

       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic ' . $Authorisation_Header
            ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        self::updateTokens($server_output);
    }

    static function isAuthorised(){
        return isSet( $_SESSION['access_token'] );
    }

    static function updateTokens($token){
        $token = json_decode($token, true);
        $_SESSION['access_token'] = $token['access_token'];
        $_SESSION['access_token_created_time'] = time();
        $_SESSION['access_token_expires_in'] = $token['expires_in'];
        $_SESSION['refresh_token'] = $token['refresh_token'];
    }

    static function getAuthorisedUsersDetails(){
        $endpoint = 'v1/me';

        $res = self::endpointGet($endpoint);
        return json_decode($res, true);
    }

    static function getAuthorisedUsersProfileLink(){
        return self::getAuthorisedUsersDetails()['external_urls']['spotify'];
    }

    static function endpointGet($endpoint, $getParams = array()){
        $base = 'https://api.spotify.com/';
        $url = $base . $endpoint;
        if ( count($getParams) != 0 ) {
            $query = http_build_query($getParams);
            $url .= '?' . $query;
        };

        // Do we need to generate a new token?
        $createdAt = $_SESSION['access_token_created_time'];
        $expiresIn = $_SESSION['access_token_expires_in'];
        if ( $createdAt > ($createdAt + $expiresIn))
            self::refresh_token();

        // Make cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);

        // To prevent MITM attacks comment out the following 2 lines
        // And setup your PHP's cURL certificate information properly
        //
        // https://stackoverflow.com/a/14064903
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $_SESSION['access_token']
            ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        return $server_output;
    }
    static function endpointPut($endpoint, $putParams = array()){
        $base = 'https://api.spotify.com/';
        $url = $base . $endpoint;

        // Do we need to generate a new token?
        $createdAt = $_SESSION['access_token_created_time'];
        $expiresIn = $_SESSION['access_token_expires_in'];
        if ( $createdAt > ($createdAt + $expiresIn))
            self::refresh_token();

        // Make cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        if ( count($putParams != 0 ))
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($putParams));

        // To prevent MITM attacks comment out the following 2 lines
        // And setup your PHP's cURL certificate information properly
        //
        // https://stackoverflow.com/a/14064903
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $_SESSION['access_token']
            ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        return $server_output;
    }
    static function endpointPost($endpoint, $postParams = array()){
        $base = 'https://api.spotify.com/';
        $url = $base . $endpoint;

        // Do we need to generate a new token?
        $createdAt = $_SESSION['access_token_created_time'];
        $expiresIn = $_SESSION['access_token_expires_in'];
        if ( $createdAt > ($createdAt + $expiresIn))
            self::refresh_token();

        // Make cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        if ( count($postParams != 0 ))
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($postParams));

        // To prevent MITM attacks comment out the following 2 lines
        // And setup your PHP's cURL certificate information properly
        //
        // https://stackoverflow.com/a/14064903
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $_SESSION['access_token']
            ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        return $server_output;
    }

    static function get_player_info(){
        $endpoint = 'v1/me/player';
        return self::endpointGet($endpoint);
    }
    static function player_playpause(){
        $res = json_decode(self::get_player_info(),true);
        if ($res['is_playing'])
            return self::player_pause();
        else
            return self::player_play();
    }
    static function player_play(){
        return self::endpointPut('v1/me/player/play');
    }
    static function player_pause(){
        return self::endpointPut('v1/me/player/pause');
    }
    static function player_previous(){
        return self::endpointPost('v1/me/player/previous');
    }
    static function player_next(){
        return self::endpointPost('v1/me/player/next');
    }
    static function player_shuffle(){
        $bool = !json_decode(self::get_player_info(),true)['shuffle_state'];
        $boolString = ($bool) ? 'true' : 'false';
        return self::endpointPut('v1/me/player/shuffle?state='.$boolString);
    }
    static function player_repeat(){
        $currentState = json_decode(self::get_player_info(),true)['repeat_state'];

        $state = 'off';
        if ( $currentState == 'off' )
            $state = 'context';
        elseif( $currentState == 'context' )
            $state = 'track';

        return self::endpointPut('v1/me/player/repeat?state='.$state);
    }
    static function player_volume($volume){
        return self::endpointPut('v1/me/player/volume?volume_percent='.$volume);
    }
    static function player_currently_playing(){
        return self::endpointGet('v1/me/player/currently-playing');
    }

};

?>