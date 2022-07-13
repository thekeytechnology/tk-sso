<?php

class TkSsoCustomRole {
    public string $name;
    public bool $shouldGrantRole = false;

    /**
     * @param string $name
     * @param callable $shouldGrantRole Function to evaluate, if the current user should gain this role.
     * Do not check for other roles in the callback, as that might cause an endless loop.
     * You can use $tkSsoUser->getRole() to look at the role send from the user system.
     */
    public function __construct(string $name, callable $shouldGrantRole) {
        $this->name = $name;
        $this->shouldGrantRole = is_callable($shouldGrantRole) && call_user_func($shouldGrantRole);
    }
}