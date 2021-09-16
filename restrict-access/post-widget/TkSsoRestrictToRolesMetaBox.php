<?php

class TkSsoRestrictToRolesMetaBox {

    public static $META_KEY = "tk-sso-restrict-to-roles";

    private static $META_BOX_ID = "tkSsoRestrictAccessMetaBox";
    private static $POST_PARAM_NAME = "tkSsoRestrictToRoles";

    public function init() {
        add_action('add_meta_boxes', [$this, 'addMetaBox'], 10, 2);
        add_action('save_post', [$this, 'saveMetaBox'], 10, 1);
    }

    public function renderMetaBox($post) {

        $roleManager = new TkSsoRoleManager();
        $roles = $roleManager->getRolesForRestriction();
        $selectedValues = get_post_meta($post->ID, $this::$META_KEY, true);

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
    }
}
