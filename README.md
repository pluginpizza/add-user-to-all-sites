# Add User to All Sites WordPress CLI Command

A WP-CLI command for WordPress multisite that adds an existing user to every site in the network in one go.

## Options

**\<user\>…**

The user login, user email or user ID of the user(s) to update.

**role**

A string used to set the user’s role on each site. Defaults to `subscriber` if not set. If the user already exists on a site they will keep their existing role.

### Examples

```
wp add-user-to-all-sites 123 --role=administrator
wp add-user-to-all-sites bob --role=editor
wp add-user-to-all-sites bob@example.com
```
