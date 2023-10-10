<?php

class TkSsoRestrictToRolesMetaBox {

    public static string $META_KEY_WHITELIST = "tk-sso-restrict-to-roles";
    public static string $META_KEY_REDIRECT = "tk-sso-restrict-to-roles-redirect";
    public static string $STRING_REPLACE_URL = "{{url}}";

    private static string $META_BOX_ID = "tkSsoRestrictAccessMetaBox";
    private static string $POST_PARAM_NAME_WHITELIST = "tkSsoRestrictToRolesWhitelist";
    private static string $POST_PARAM_NAME_REDIRECT = "tkSsoRestrictToRolesRedirect";

    public function init() {
        add_action('add_meta_boxes', [$this, 'addMetaBox'], 10, 2);
        add_action('save_post', [$this, 'saveMetaBox'], 10, 1);
    }

    public function renderMetaBox($post) {
        $roleManager = new TkSsoRoleManager();
        $roles = $roleManager->getRolesForRestriction();
        $whitelistValues = is_array($temp = get_post_meta($post->ID, $this::$META_KEY_WHITELIST, true)) ? $temp : array();
        $redirect = get_post_meta($post->ID, $this::$META_KEY_REDIRECT)[0] ?? "";
        if (is_array($redirect)) {
            $redirect = $redirect[0] ?? "";
        }
        ?>
        <div style="font-size: 14px;">
            <strong>Rollen auswählen:</strong>
            <div class="tk-meta-box-multiselect" style="border: 1px solid #e5e5e5; padding: 10px; border-radius: 5px; background-color: #f9f9f9; margin-bottom: 20px; margin-top: 6px">
                <ul id="tkSso-restrict-access-roles-list" class="categorychecklist form-no-clear" style="list-style-type: none; margin: 0; padding: 0;">
                    <?php foreach ($roles as $role) {
                        $checked = in_array($role, $whitelistValues) ? "checked='checked'" : "";
                        echo "<li id='$role' style='margin-bottom: 8px;'><label class='selectit'><input value='$role' type='checkbox' style='margin-right: 10px;'
                                                                  name='{$this::$POST_PARAM_NAME_WHITELIST}[]'
                                                                  id='$role' $checked> $role</label></li>";
                    } ?>
                </ul>
                <input type="hidden" name="<?php echo "{$this::$POST_PARAM_NAME_WHITELIST}[]" ?>" value=""/>
            </div>

            <span style="color: #d9534f; font-size: 14px; display: block; padding: 10px 15px; border: 1px solid #d9534f; border-radius: 5px; background-color: #fdf2f2; margin-bottom: 20px;">Hinweis: Bei Auswahl von "Aus Deutschland" oder "Aus Österreich" ist ein Login erforderlich, um Zugang zu dieser Seite zu erhalten. Bitte stellen Sie sicher, dass Sie auch eine Anmeldeoption aktivieren.</span>

            <hr style="border: 0; border-top: 1px solid #e5e5e5; margin-bottom: 20px;">

            <strong>Benutzergruppen:</strong>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; margin-top: 6px;">
                <tr>
                    <td style="padding: 8px; border: 1px solid #e5e5e5; font-weight: bold;">UG1</td>
                    <td style="padding: 8px; border: 1px solid #e5e5e5;">Arzt und Apotheker</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #e5e5e5; font-weight: bold;">UG2</td>
                    <td style="padding: 8px; border: 1px solid #e5e5e5;">Arzt, Apotheker, Hebamme und PTA</td>
                </tr>
            </table>

            <hr style="border: 0; border-top: 1px solid #e5e5e5; margin-bottom: 20px;">

            <strong>Redirect Einstellungen:</strong>
            <small style="color: #555; display: block; margin-bottom: 10px;">Custom Redirect wenn Nutzer nicht die entsprechenden Rollen hat. {{url}} entspricht der aktuellen Url. <br/>Beispiel: /login/?redirectTo={{url}}</small>
            <input type="text" name="<?php echo $this::$POST_PARAM_NAME_REDIRECT ?>" value="<?php echo $redirect ?>" />
        </div>

        <?php
    }

    public function addMetaBox($postType, $post) {
        add_meta_box(
            $this::$META_BOX_ID,
            'Zugriff beschränken',
            [$this, 'renderMetaBox'],
            $postType,
            'side',
            'default'
        );
    }



    public function saveMetaBox($postId) {
        $whitelistValues = $_POST[$this::$POST_PARAM_NAME_WHITELIST] ?? false;
        if ($whitelistValues !== false) {

            $filteredWhitelistValues = array_filter($whitelistValues, function ($val) {
                return $val !== "";
            });

            update_post_meta($postId, $this::$META_KEY_WHITELIST, $filteredWhitelistValues);
        }


        $redirect = $_POST[$this::$POST_PARAM_NAME_REDIRECT] ?? false;

        if ($redirect !== false) {
            update_post_meta($postId, $this::$META_KEY_REDIRECT, $redirect);
        }
    }
}

