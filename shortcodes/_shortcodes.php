<?php

add_shortcode('tk-sso-simple-login-form', 'tkSsoSimpleLoginForm');

function tkSsoSimpleLoginForm()
{
    global $tkSsoUser;
    if ($tkSsoUser->isLoggedIn()) return '';
    return '
        <div id="tkSsoDemoLoginForm" class="wrapper fadeInDown" >
          <div id="formContent">
            <!-- Tabs Titles -->
            <h2 class="active"> Sign In </h2>
            <h2 class="inactive underlineHover">Sign Up </h2>
        
            <!-- Icon -->
            <div class="fadeIn first">
              <i class="fas fa-user"></i>
            </div>
        
            <!-- Login Form -->
            <form>
               <div class="tkSSoSpinner"> 
                    <img src="' . get_home_url() . '/wp-content/plugins/tk-sso/assets/images/tk-sso-spinner.gif' . '">
               </div>
              <input type="text" id="userName" class="fadeIn second" name="login" placeholder="login">
              <input type="password" id="password" class="fadeIn third" name="login" placeholder="password">
              <a type="submit" class="fadeIn fourth submit" id="tkSsoLogIn" >Log In</a>
              <p  id="tkSsoError" class="fadeIn third"></p>
            </form>
        
            <!-- Remind Passowrd -->
            <div id="formFooter">
              <a class="underlineHover" href="#">Logout</a>
            </div>
            ' . do_shortcode('[tk-doccheck-login-btn]') . '
          </div>
        </div>
    ';
}

function tkSsoGetUserDataShortcode($atts = [])
{
    $key = $atts['key'] ?? "";

    if ($key) {
        global $tkSsoUser;
        return $tkSsoUser->getData($key);
    }

    return "";
}

add_shortcode("tk-sso-user-data", "tkSsoGetUserDataShortcode");


/**
 * Doccheck Login Button
 * Usage:
 * custom values: [tk-doccheck-login-btn btnValue="test" classes="class1 class2" target="_self"]
 * default values: [tk-doccheck-login-btn]
 */
add_shortcode("tk-doccheck-login-btn", "tkDoccheckLoginBtn");
function tkDoccheckLoginBtn($attr)
{
    global $tkSsoBroker;
    if ($tkSsoBroker->isUserLoggedIn()) return '';
    if (!get_option("tkt_use_sso_v2")) return '';

    $args = shortcode_atts(array(
        "btnValue" => "Login mit Doccheck",
        "classes" => "",
        "target" => "_blank",
        "generalDoccheckUrl" => get_option('tkt_sso_server_url') . "/doccheck"
    ), $attr);

    $url = home_url($_SERVER['REQUEST_URI']);
    if (function_exists("tk_parse_url") && function_exists("tk_build_url")) {
        $parsed = tk_parse_url($url);
        $parsed['query']['loggedIn'] = true;
        $url = tk_build_url($parsed);
    }

    $redirectBase64 = base64_encode($url);
    $brandId = "Brand:" . get_option("tkt_broker_id");
    $doccheckLoginUrlForCurrentPage = $args["generalDoccheckUrl"] . "/$brandId/$redirectBase64";
    return '
        <a target="' . $args["target"] . '" id="tkDocCheckLoginBtn" class="' . $args["classes"] . '" href="' . $doccheckLoginUrlForCurrentPage . '" data-tk-brand-id="' . $brandId . '">' . $args["btnValue"] . '</a>';
}

