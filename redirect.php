<?php

    require_once('./config.php');
    require_once('./functions.php');

    @session_start();

    if(empty($_GET['code'])) {

        $_SESSION['state'] = sha1(uniqid(mt_rand(), TRUE));

        $params = [
            'client_id'      => CLIENT_ID,
            'redirect_uri'   => SITE_URL.'redirect.php',
            'state'          => $_SESSION['state'],
            'approval_promt' => 'force',
            'scope'          => 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email',
            'response_type'  => 'code'
        ];

        $url = 'https://accounts.google.com/o/oauth2/auth?'.http_build_query($params);
        header('Location: '.$url);
        exit;

    } else {

        if($_SESSION['state'] != $_GET['state']) {
            echo 'NO!';
            exit;
        }

        $params = [
            'client_id'     => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'code'          => $_GET['code'],
            'redirect_uri'  => SITE_URL.'redirect.php',
            'grant_type'    => 'authorization_code'
        ];

        $url = 'https://accounts.google.com/o/oauth2/token';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $rs = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($rs);

        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$json->access_token;
        $me  = json_decode(file_get_contents($url));

        $dbh  = connectDb();
        $sql  = "select * from users where google_user_id = :id limit 1";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
                           ":id" => $me->id
                       ]);
        $user = $stmt->fetch();

        if(!$user) {

            $sql    = "insert into users(google_user_id, google_email, google_name, google_picture, google_access_token, created, modified)
												values
												(:google_user_id, :google_email, :google_name, :google_picture, :google_access_token, now(), now())";
            $stmt   = $dbh->prepare($sql);
            $params = [
                ":google_user_id"      => $me->id,
                ":google_email"        => $me->email,
                ":google_name"         => $me->name,
                ":google_picture"      => $me->picture,
                ":google_access_token" => $json->access_token
            ];
            $stmt->execute($params);

            $myId = $dbh->lastInsertId();
            $sql  = "select * from users where id = :id limit 1";
            $stmt->execute([":id" => $myId]);
            $user = $stmt->fetch();

        }

        if(!empty($user)) {
            session_regenerate_id(TRUE);
            $_SESSION['me'] = $user;
        }

        header('Location: '.SITE_URL);

    }
