<?php

class TkSsoSettingsPage {

    public static $OPTION_SERVER_URL = "tkt_sso_server_url";
    public static $OPTION_LOGIN_URL = "tkt_sso_login_url";

    public function init() {
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', [$this, 'registerOptionsPage']);
    }

    public function registerSettings() {
        $this->registerSetting($this::$OPTION_SERVER_URL, "https://www.mypersonaltrainer.de/wp-content/themes/tkt/sso-server/api.php");
        $this->registerSetting($this::$OPTION_LOGIN_URL, "");

    }

    private function registerSetting($name, $default = '') {
        add_option($name, $default);
        register_setting('tkt_sso_options_group', $name);
    }


    public function registerOptionsPage() {
        add_options_page('TKT SSO plugin page', 'TKT SSO', 'manage_options', 'tk-sso', [$this, 'renderOptionsPage']);
    }


    public function renderOptionsPage() {
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
                            <label for="<?php echo $this::$OPTION_SERVER_URL ?>">SSO server url</label>
                        </th>
                        <td>
                            <input name="<?php echo $this::$OPTION_SERVER_URL ?>" type="text"
                                   id="<?php echo $this::$OPTION_SERVER_URL ?>"
                                   value="<?php echo get_option($this::$OPTION_SERVER_URL); ?>"
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
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
