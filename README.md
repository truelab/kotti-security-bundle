Truelab Kotti Security Bundle
=============================

A symfony2 bundle that provides "security" integration, admin features for kotti frontend. 
@see [TruelabKottiFrontendBundle](https://github.com/truelab/kotti-frontend-bundle)


***This bundle is currently under development, the API must not be considered stable.***

[![Build Status](https://api.travis-ci.org/truelab/kotti-security-bundle.svg)](https://travis-ci.org/truelab/kotti-security-bundle)


## Install

Adds this to your composer.json and run ```composer update truelab/kotti-security-bundle```:

```json
{
    "require": {
        "truelab/kotti-security-bundle" : "~0.1"
    },
    "repositories" : [
        { "type":"git", "url":"https://github.com/truelab/kotti-security-bundle.git" }
    ]
}    
```    

Updates your AppKernel.php: 

```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Truelab\KottiSecurityBundle\TruelabKottiSecurityBundle(),
        );
        
        return $bundles;
    }
}
```    
    
Adds this to to your configuration:

```yaml
# app/config/config.yml
truelab_kotti_security:
    auth:
        secret: your_kotti_pyramid_authentication_secret
```


or better, use a parameter:

```yaml
# app/config/config.yml
truelab_kotti_security:
    auth:
        secret: %kotti.authentication_secret%
```

## Usage

To activate and use the firewall that provides authentication reading the ```auth_tkt``` cookie, 
adds ```kotti``` key to a firewall config.

```yaml
# app/config/security.yml
security:
    # declare kotti user provider (optional but recommended)
    providers:
        the_kotti_user_provider:
            id: truelab_kotti_security.user_provider 
    # ...
    firewalls:
        kotti_firewall:
            anonymous: ~         # permits anonymous user
            kotti: ~             # activates kotti authentication provider
            stateless: true      # !!!important is stateless, we rely only on the presence of a valid auth_tkt cookie
            # use kotti user provider for this firewall 
            # to have a Truelab\KottiSecurityBundle\Entity\User instance as authenticated user
            provider: the_kotti_user_provider 
```

### AdminBar

To render a simple bootstrap based admin bar use the ```render``` controller method.
 
eg.

```twig
# mylayout.html.twig

{% block admin_bar %}
    {{ render(controller('TruelabKottiSecurityBundle:AdminBar:view') }}
{% endblock %}
```

### Act as anonymous

When activated this feature permits to an admin user to act/see the site as an anonymous user ( no "private" contents, etc. ).
To activate this feature set ```act_as_anonymous``` config option to ```true```.
 
eg.

```yaml

truelab_kotti_security:
    auth:
        secret: KottiSecret # !!!required
    act_as_anonymous: true  # activate see as feature     
  
```  

then imports kotti security bundle routes in your app routing file (eg. ```app/config/routing.yml```).

eg.

```yaml

_kotti_security:
    prefix: '/kotti_security'
    resource: "@TruelabKottiSecurityBundle/Resources/config/routing.xml"
```  

#### Twig Http Kernel Extension Override

When the related option (```truelab_kotti_security.twig.http_kernel_extension_override```) is set to true
we override some twig functions: ```render_esi```.
In our case we bypass render_esi when a connected user is an admin and is not acting as anonymous.

eg.

```yaml

truelab_kotti_security:
    auth:
        secret: KottiSecret # !!!required
    twig:
        http_kernel_extension_override: true # default it's false
```

## Configuration reference

```yaml

truelab_kotti_security:
    auth:
        secret: KottiPyramidAuthenticationSecretValue  # !!!required
        cookie_name: auth_tkt
        hash_alg: sha512
        include_ip: false
    act_as_anonymous: false    
    twig:
        http_kernel_extension_override: false
```            
             
