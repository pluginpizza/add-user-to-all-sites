# Add User to All Sites WordPress CLI Command

Registers a CLI command that allows you to add a user to all sites in a multisite network.

## Use

`wp add-user-to-all-sites 123 --role=administrator`

## Options

**\<user\>…**

The user login, user email or user ID of the user(s) to update.

**role**

A string used to set the user’s role on each site. Defaults to `subscriber` if not set. If the user already exists on a site their existing role will be updated.
