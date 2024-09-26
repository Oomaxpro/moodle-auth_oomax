Date: January 2023  
Name: oomax  
Url: /auth/oomax/index.php?tokem=&logout=

### Enable plugin ###

Site Administration -> Plugins -> Authentication -> Manage Authentication

### Configure the plugin. ###

1. oomax public key:  
oomax key to decode user token data.   
oomax key can be retrieved from: 
https://oomax-idp.region.amazonaws.com/userPoolId/.well-known/jwks.json

### Plugin functionality ###

Purpose of this plugin is to receive oomax user token and allow user access to LMS.

**1. oomax user token.**  
Plugin expect token as required query param in url.  
Inside token system expect following attributes:
```bash
name
family_name
email
locale
```

**2. User doesn't exist in LMS.**  
If user doesn't exist in LMS, plugin will create user with following data:
```bash
firstname: name from oomax token
lastname: family_name from oomax token
email: email from oomax token
username: email from oomax token
locale: locale from oomax token
auth: oomax
mnethostid: 1
confirmed: 1
suspended: 0
lastlogin: 0
```
After successful creation user will be automatically logged in.

**3. User exist in LMS.**   
If user exist in LMS, plugin will update user with following data:
```bash
locale: locale from oomax token
```
After successful update user will be automatically logged in.


### Logout redirection ### 

**logout:** login page where user need to be redirected


Plugin expect logout url as required query param in url.  
Plugin will store that inside Session object and use it when user click logout.  
After user click logout inside LMS system will redirect him back to this parameter.
