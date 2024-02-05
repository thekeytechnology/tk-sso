<?php

class TkSsoSettingsPage
{


    public static $BROKER_ID = "tkt_broker_id";
    public static $USE_STAGING_API = "tkt_use_staging_api";


    public function init()
    {
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', [$this, 'registerOptionsPage']);
    }

    public function registerSettings()
    {
        $this->registerSetting($this::$BROKER_ID, "");
        $this->registerSetting($this::$USE_STAGING_API, "0");
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
                            <label for="<?php echo $this::$BROKER_ID ?>">Broker ID</label>
                        </th>
                        <td>
                            <input name="<?php echo $this::$BROKER_ID ?>" type="text"
                                   id="<?php echo $this::$BROKER_ID ?>"
                                   value="<?php echo get_option($this::$BROKER_ID); ?>"
                                   class="regular-text code">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $this::$USE_STAGING_API ?>">Staging API benutzen?</label>
                        </th>
                        <td>
                            <input class="checkbox"
                                   type="checkbox" <?php checked(get_option($this::$USE_STAGING_API), 'on'); ?>
                                   id="<?php echo $this::$USE_STAGING_API; ?>" name="<?php echo $this::$USE_STAGING_API; ?>"/>
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
