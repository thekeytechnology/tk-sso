# TheKey Technology Single Sign-On (SSO) Plugin



**Key Features:**

- **Effortless Single Sign-On (SSO):** Users can effortlessly navigate between different WordPress instances without repeated logins.

- **User Role Validation:** Administrators can control content access based on assigned user roles.

- **Granular Content Restriction:** Fine-tune access to WP Posts, Pages, Custom Posts, and Elementor Widgets, providing precise control over content elements.

- **Country-based Validation:** Verify users based on their country of origin, enhancing security and content control.

- **Time-Based Content Restriction:** Admins can set time-based restrictions on content elements for precise control over visibility.


**Usage:**

1. Activate the plugin.
2. Configure settings by entering the Broker ID and selecting Staging or Production APIs.

**Important Note:**

- **External Server Operations:** The plugin delegates login, registration, and logout processes to an external server Identity Provider: [https://identity.infectopharm.com/](https://identity.infectopharm.com/).


## Version 90

**Release Notes:**

### Changes in this Version:

- **JWT Implementation:** The plugin has undergone significant changes, moving away from the previous `tkSSToken` cookie for authentication. Instead, it now employs JSON Web Tokens (JWT) for improved security and streamlined token management.

- **Transition from Cross-Site Cookie Sharing:** The plugin no longer relies on cross-site cookie sharing. Instead, a more secure and efficient redirect principle has been implemented to facilitate communication between different components.

- **Code Refactoring:** In this release, extensive code refactoring has taken place. This refactoring aims to enhance readability, maintainability, and overall code clarity. Developers and contributors will find the updated codebase more understandable and easier to navigate.

### Breaking Changes:

- **Token Mechanism Update:** Due to the adoption of JWT, there are breaking changes in the token mechanism. Ensure your system is compatible with this updated approach.

- **Codebase Adjustments:** Developers and contributors should be aware of the extensive code changes. Adjust customizations and enhancements accordingly, considering the refactored codebase.

