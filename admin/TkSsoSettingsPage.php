<?php

class TkSsoSettingsPage
{

    public static $OPTION_SERVER_URL = "tkt_sso_server_url";
    public static $OPTION_LOGIN_URL = "tkt_sso_login_url";
    public static $OPTION_LOGIN_REDIRECT_URL = "tkt_sso_login_redirect_url";
    public static $OPTION_ACCEPT_WORDPRESS_LOGIN = "tkt_sso_accept_wordpress_login";
    public static $USE_SSO_V2 = "tkt_use_sso_v2";
    public static $BROKER_ID = "tkt_broker_id";
    public static $DEVELOPMENT_MODE = "tk-development-mode";

    public function init()
    {
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', [$this, 'registerOptionsPage']);
    }

    public function registerSettings()
    {
        $this->registerSetting($this::$OPTION_SERVER_URL, "https://cme-portal.infectopharm.com/api/tkt-sso/server");
        $this->registerSetting($this::$OPTION_LOGIN_URL, "");
        $this->registerSetting($this::$OPTION_LOGIN_REDIRECT_URL, "/");
        $this->registerSetting($this::$OPTION_ACCEPT_WORDPRESS_LOGIN, "0", ["description" => "0 = Nur der Login via Login Formular kann geschützte Inhalte freigeben. 1 = Wer im Wordpress angemeldet ist, wird behandelt, als wäre er via Drupal angemeldet."]);
        $this->registerSetting($this::$USE_SSO_V2, "0");
        $this->registerSetting($this::$BROKER_ID, "");
        $this->registerSetting($this::$DEVELOPMENT_MODE, "0", ["description" => "Erstellt einen neue Seite (domain/Tk-SSO-Developer-Login-Page) NUR für Entwicklungszwecke"]);
    }

    private function registerSetting($name, $default = '', $args = [])
    {
        add_option($name, $default);
        register_setting('tkt_sso_options_group', $name, $args);
    }


    public function registerOptionsPage()
    {
        add_options_page('TKT SSO plugin page', 'TKT SSO', 'manage_options', 'tk-sso', [$this, 'renderOptionsPage']);
    }


    public function renderOptionsPage()
    {
        $useV2 = get_option($this::$USE_SSO_V2);
        ?>
        <div>
            <?php screen_icon(); ?>
            <h2>TKT SSO Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields('tkt_sso_options_group'); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $this::$USE_SSO_V2 ?>">SSO V2 benutzen?</label>
                        </th>
                        <td>
                            <input class="checkbox"
                                   type="checkbox" <?php checked(get_option($this::$USE_SSO_V2), 'on'); ?>
                                   id="<?php echo $this::$USE_SSO_V2; ?>" name="<?php echo $this::$USE_SSO_V2; ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $this::$BROKER_ID ?>">Broker ID</label>
                        </th>
                        <td>
                            <input name="<?php echo $this::$BROKER_ID ?>" type="text"
                                   id="<?php echo $this::$BROKER_ID ?>"
                                   value="<?php echo get_option($this::$BROKER_ID); ?>"
                                   class="regular-text code">
                        </td>
                    </tr>
                    <?php if (!$useV2) : ?>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $this::$OPTION_SERVER_URL ?>">SSO server url</label>
                        </th>
                        <td>
                            <input name="<?php echo $this::$OPTION_SERVER_URL ?>" type="text"
                                   id="<?php echo $this::$OPTION_SERVER_URL ?>"
                                   value="<?php echo get_option($this::$OPTION_SERVER_URL) ?? $this::registerSettings(); ?>"
                                   class="regular-text code">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $this::$OPTION_LOGIN_URL ?>">SSO Login url</label>
                        </th>
                        <td>
                            <input name="<?php echo $this::$OPTION_LOGIN_URL ?>" type="text"
                                   id="<?php echo $this::$OPTION_LOGIN_URL ?>"
                                   value="<?php echo get_option($this::$OPTION_LOGIN_URL); ?>"
                                   class="regular-text code">
                        </td>
                    </tr>
                    <tr>
                        <?php endif; ?>
                        <th scope="row">
                            <label for="<?php echo $this::$OPTION_LOGIN_REDIRECT_URL ?>">SSO Login Standard Redirect
                                Url</label>
                        </th>
                        <td>
                            <input name="<?php echo $this::$OPTION_LOGIN_REDIRECT_URL ?>" type="text"
                                   id="<?php echo $this::$OPTION_LOGIN_REDIRECT_URL ?>"
                                   value="<?php echo get_option($this::$OPTION_LOGIN_REDIRECT_URL); ?>"
                                   class="regular-text code">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $this::$OPTION_ACCEPT_WORDPRESS_LOGIN ?>">Wordpress Login als SSO
                                Login betrachten? 0 = Nein, 1 = Ja</label>
                        </th>
                        <td>
                            <input name="<?php echo $this::$OPTION_ACCEPT_WORDPRESS_LOGIN ?>" type="text"
                                   id="<?php echo $this::$OPTION_ACCEPT_WORDPRESS_LOGIN ?>"
                                   value="<?php echo get_option($this::$OPTION_ACCEPT_WORDPRESS_LOGIN); ?>"
                                   class="regular-text code">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $this::$DEVELOPMENT_MODE ?>"><span style="font-weight: 900;">Development Mode?</span> <br> Erstellt einen neue Seite <a target="_blank" href="<?php echo get_home_url() . "/Tk-SSO-Developer-Login-Page"; ?>">Tk-SSO-Developer-Login-Page</a> NUR für Entwicklungszwecke</label>
                        </th>
                        <td>
                            <input class="checkbox"
                                   type="checkbox" <?php checked(get_option($this::$DEVELOPMENT_MODE), 'on'); ?>
                                   id="<?php echo $this::$DEVELOPMENT_MODE; ?>"
                                   name="<?php echo $this::$DEVELOPMENT_MODE; ?>"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
