<?php

class TkSsoRestrictToRolesMetaBox {

    public static $META_KEY = "tk-sso-restrict-to-roles";
    public static $META_KEY_REDIRECT = "tk-sso-restrict-to-roles-redirect";
    public static $STRING_REPLACE_URL = "{{url}}";

    private static $META_BOX_ID = "tkSsoRestrictAccessMetaBox";
    private static $POST_PARAM_NAME = "tkSsoRestrictToRoles";
    private static $POST_PARAM_NAME_REDIRECT = "tkSsoRestrictToRolesRedirect";

    public function init() {
        add_action('add_meta_boxes', [$this, 'addMetaBox'], 10, 2);
        add_action('save_post', [$this, 'saveMetaBox'], 10, 1);
    }

    public function renderMetaBox($post) {

        $roleManager = new TkSsoRoleManager();
        $roles = $roleManager->getRolesForRestriction();
        $selectedValues = get_post_meta($post->ID, $this::$META_KEY, true);
        $redirect = get_post_meta($post->ID, $this::$META_KEY_REDIRECT)[0] ?? "";
        if (is_array($redirect)) {
            $redirect = $redirect[0] ?? "";
        }

        ?>
        <div class="tk-meta-box-multiselect">
            <!--            <input type="hidden" name="--><?php //echo $this::$POST_PARAM_NAME ?><!--" value="0">-->
            <ul id="tkSso-restrict-access-roles-list" class="categorychecklist form-no-clear">
                <?php foreach ($roles as $role) {
                    $checked = in_array($role, $selectedValues) ? "checked='checked'" : "";
                    echo "<li id='$role'><label class='selectit'><input value='$role' type='checkbox'
                                                                      name='{$this::$POST_PARAM_NAME}[]'
                                                                      id='$role' $checked> $role</label></li>";
                } ?>
            </ul>
            <input type="text" name="<?php echo $this::$POST_PARAM_NAME_REDIRECT ?>" value="<?php echo $redirect ?>"/>
            <small>Custom Redirect wenn Nutzer nicht die entsprechenden Rollen hat. {{url}} entspricht der aktuellen
                Url. Beispiel: /login/?redirectTo={{url}}</small>
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
        $selectedValues = $_POST[$this::$POST_PARAM_NAME] ?? [];

        update_post_meta($postId, $this::$META_KEY, $selectedValues);

        $redirect = $_POST[$this::$POST_PARAM_NAME_REDIRECT] ?? [];

        update_post_meta($postId, $this::$META_KEY_REDIRECT, $redirect);
    }
}
