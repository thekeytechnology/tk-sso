<?php

add_shortcode('tk-sso-simple-login-form', 'tkSsoSimpleLoginForm');

function tkSsoSimpleLoginForm()
{
    global $tkSsoUser;
    if ($tkSsoUser->isLoggedIn()) return '';
    return '
        <div class="wrapper fadeInDown">
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
              <input type="text" id="password" class="fadeIn third" name="login" placeholder="password">
              <a type="submit" class="fadeIn fourth submit" id="tkSsoLogIn" >Log In</a>
              <p  id="tkSsoError" class="fadeIn third"></p>
            </form>
        
            <!-- Remind Passowrd -->
            <div id="formFooter">
              <a class="underlineHover" href="#">Logout</a>
            </div>
        
          </div>
        </div>
    ';
}
