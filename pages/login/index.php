<?php

    $invalid = false;

    include('../../config.php');

    function Redirect($url, $permanent = false)
    {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    }

    if($_GET) {
        $timezone_name = timezone_name_from_abbr("", $_GET['time_offset']*60, false);
        setcookie('time_offset', $timezone_name, time() + (86400 * 30), "/");
    }

    if($_POST) {

        //check if all required values are filled
        if( !empty($_POST['login-email']) && !empty($_POST['login-password']) )
        {
            // make db instance
            $db = new Sqlite3('../../database.sqlite', SQLITE3_OPEN_READWRITE);
            //get user and set token
            $query = "select * from users where password='" . md5($_POST['login-email'] . $_POST['login-password']) . "'";
            $result = $db->querySingle($query, true);
            if(empty($result)) {

                // invalid email or password combination
                $invalid = true;

            } else {
                $invalid = false;
                // set cookie and redirect to tracker
                setcookie('user_id', $result['id'], time() + (86400 * 30), "/");
                setcookie('locale', $_POST['login-locale'], time() + (86400 * 30), "/");
                Redirect($edulog_root . 'pages/tracker', false);
            }

        } else {
            $invalid = true;
        }
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Edulog login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">
        <link rel="stylesheet" href="../style.css" type="text/css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
        <script src="login.js"></script>
    </head>

    <body>
        <div class="site-content">
            <?php include "../../" . 'pages/navbar/navbar.php'; ?>

            <form id="login-form" action="<?=$_SERVER['PHP_SELF'];?>" method="post" class="logreg">
                <section class="box-head">
                    <h1 id="title">Logi sisse</h1>
                </section>
                <section>
                    <label id="label-locale"for="register-locale">Keel</label>
                    <select id="login-locale" name="login-locale">
                        <option value="et">Eesti</option>
                        <option value="en">English</option>
                    </select>
                </section>
                <section>
                    <label id="label-email" for="login-email">E-mail</label>
                    <input id="login-email" name="login-email">
                </section>
                <section>
                    <label id="label-password" for="login-email">Salasõna</label>
                    <input type="password" id="login-password" name="login-password">
                </section>
                <section>
                    <div class="btn-wrap">
                        <button id="login-btn" class="f-btn" type="submit">Edasi</button>
                    </div>
                </section>
            </form>
            <?php if($invalid) {
                echo '<span style="color: red; font-weight: bold; margin-top: 10px;" id="invalid-combination">Vale salasõna või emaili kombinatsioon</span>';
            } ?>
            <a id="cta-register" href="../register" class="register-cta">Ei ole veel kasutaja? Registreeru siin</a>
        </div>
    </body>
</html>
