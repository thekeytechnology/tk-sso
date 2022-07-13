<?php

class TkSsoRestrictToRolesMetaBox {

    public static string $META_KEY_WHITELIST = "tk-sso-restrict-to-roles";
    public static string $META_KEY_BLACKLIST = "tk-sso-restrict-to-roles-blacklist";
    public static string $META_KEY_REDIRECT = "tk-sso-restrict-to-roles-redirect";
    public static string $STRING_REPLACE_URL = "{{url}}";

    private static string $META_BOX_ID = "tkSsoRestrictAccessMetaBox";
    private static string $POST_PARAM_NAME_WHITELIST = "tkSsoRestrictToRolesWhitelist";
    private static string $POST_PARAM_NAME_BLACKLIST = "tkSsoRestrictToRolesBlacklist";
    private static string $POST_PARAM_NAME_REDIRECT = "tkSsoRestrictToRolesRedirect";

    public function init() {
        add_action('add_meta_boxes', [$this, 'addMetaBox'], 10, 2);
        add_action('save_post', [$this, 'saveMetaBox'], 10, 1);
    }

    public function renderMetaBox($post) {

        $roleManager = new TkSsoRoleManager();
        $roles = $roleManager->getRolesForRestriction();
        $whitelistValues = get_post_meta($post->ID, $this::$META_KEY_WHITELIST, true);
        $blacklistValues = get_post_meta($post->ID, $this::$META_KEY_BLACKLIST, true);
        $redirect = get_post_meta($post->ID, $this::$META_KEY_REDIRECT)[0] ?? "";
        if (is_array($redirect)) {
            $redirect = $redirect[0] ?? "";
        }

        ?>
        Einschließen:
        <div class="tk-meta-box-multiselect">
            <ul id="tkSso-restrict-access-roles-list" class="categorychecklist form-no-clear">
                <?php foreach ($roles as $role) {
                    $checked = in_array($role, $whitelistValues) ? "checked='checked'" : "";
                    echo "<li id='$role'><label class='selectit'><input value='$role' type='checkbox'
                                                                      name='{$this::$POST_PARAM_NAME_WHITELIST}[]'
                                                                      id='$role' $checked> $role</label></li>";
                } ?>
            </ul>
            <input type="hidden" name="<?php echo "{$this::$POST_PARAM_NAME_WHITELIST}[]" ?>" value=""/>
        </div>
        Ausschließen:
        <div class="tk-meta-box-multiselect">
            <ul id="tkSso-restrict-access-roles-list" class="categorychecklist form-no-clear">
                <?php foreach ($roles as $role) {
                    $checked = in_array($role, $blacklistValues) ? "checked='checked'" : "";
                    echo "<li id='$role'><label class='selectit'><input value='$role' type='checkbox'
                                                                      name='{$this::$POST_PARAM_NAME_BLACKLIST}[]'
                                                                      id='$role' $checked> $role</label></li>";
                } ?>
            </ul>
            <input type="hidden" name="<?php echo "{$this::$POST_PARAM_NAME_BLACKLIST}[]" ?>" value=""/>
        </div>
        <br/>
        <input type="text" name="<?php echo $this::$POST_PARAM_NAME_REDIRECT ?>" value="<?php echo $redirect ?>"/>
        <br/>
        <small>Custom Redirect wenn Nutzer nicht die entsprechenden Rollen hat. {{url}} entspricht der aktuellen
            Url. <br/>Beispiel: /login/?redirectTo={{url}}</small>
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
        $blacklistValues = $_POST[$this::$POST_PARAM_NAME_BLACKLIST] ?? false;

        //We do this to prevent deletion should the post be somehow saved without this field
        if ($whitelistValues !== false) {

            $filteredWhitelistValues = array_filter($whitelistValues, function ($val) {
                return $val !== "";
            });

            update_post_meta($postId, $this::$META_KEY_WHITELIST, $filteredWhitelistValues);
        }

        //We do this to prevent deletion should the post be somehow saved without this field
        if ($blacklistValues !== false) {
            $filteredBlacklistValues = array_filter($blacklistValues, function ($val) {
                return $val !== "";
            });

            update_post_meta($postId, $this::$META_KEY_BLACKLIST, $filteredBlacklistValues);
        }

        $redirect = $_POST[$this::$POST_PARAM_NAME_REDIRECT] ?? false;

        if ($redirect !== false) {
            update_post_meta($postId, $this::$META_KEY_REDIRECT, $redirect);
        }
    }
}
