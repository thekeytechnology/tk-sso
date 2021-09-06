<?php


function tkt_sso_register_settings()
{
    add_option('tkt_sso_server_url', 'https://www.mypersonaltrainer.de/wp-content/themes/tkt/sso-server/api.php');
    register_setting('tkt_sso_options_group', 'tkt_sso_server_url', 'tkt_sso_callback');
}

add_action('admin_init', 'tkt_sso_register_settings');

function tkt_sso_register_options_page()
{
    add_options_page('TKT SSO plugin page', 'TKT SSO', 'manage_options', 'tk-sso', 'tkt_sso_options_page');
}

add_action('admin_menu', 'tkt_sso_register_options_page');

function tkt_sso_options_page()
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
                        <label for="tkt_sso_server_url">SSO server url</label>
                    </th>
                    <td>
                        <input name="tkt_sso_server_url" type="text" id="tkt_sso_server_url"
                               value="<?php echo get_option('tkt_sso_server_url'); ?>" class="regular-text code"></td>
                </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}