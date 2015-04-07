Truelab Kotti Security Bundle
=============================

A symfony2 bundle that provides "security" integration, admin features for kotti frontend. 
@see [TruelabKottiFrontendBundle](https://github.com/truelab/kotti-frontend-bundle)


***This bundle is currently under development, the API must not be considered stable.***

[![Build Status](https://api.travis-ci.org/truelab/kotti-security-bundle.svg)](https://travis-ci.org/truelab/kotti-security-bundle)


## Install

Adds this to your composer.json and run ```composer update truelab/kotti-security-bundle```:

    {
        "require": {
            // ...
            "truelab/kotti-security-bundle" : "dev-dev"
        },
        "repositories" : [
            // ...
            { "type":"git", "url":"https://github.com/truelab/kotti-security-bundle.git" }
        ]
    
        //...
    }    

Updates your AppKernel.php: 

    <?php
    // ...
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
        
        // ..
    }
    
Adds this to to your configuration:


    # app/config/config.yml
    truelab_kotti_security:
        auth:
            secret: your_kotti_pyramid_authentication_secret



or better, use a parameter:

    # app/config/config.yml
    truelab_kotti_security:
        auth:
        secret: %kotti.authentication_secret%


## Usage

To activate and use the firewall that provides authentication reading the ```auth_tkt``` cookie adds ```kotti``` to one of your firewall configurations.

    # app/config/security.yml
    // ...
    
    firewalls:
        # default firewall
        default:
            anonymous: ~         # permits anonymous user
            kotti: ~             # activates kotti authentication provider
            stateless: true      # !!!important is stateless, we rely only on the presence of a valid auth_tkt cookie


## Configuration reference

```
truelab_kotti_security:
    auth:
        secret: KottiPyramidAuthenticationSecretValue  # !!!required
        cookie_name: auth_tkt
        hash_alg: sha512
```            
             
