<?php
    $user = spotify::getAuthorisedUsersDetails();
    $profile_link = spotify::getAuthorisedUsersProfileLink();
    $username = $user['id'];
    $displayName = $user['display_name'];
    $img = $user['images'][0]['url'];
?>
<div id='account' class='page'>
    <img src='<?php echo $img; ?>'/>
    <h1><?php echo $username; ?></h1>
    <h2><?php echo $displayName; ?></h2>
    <a target='_BLANK' href='<?php echo $profile_link; ?>'><button class='btn btn-green'>Goto Profile</button></a>
    <a href='index.php'><button class='btn'>Back</button></a>
</div>