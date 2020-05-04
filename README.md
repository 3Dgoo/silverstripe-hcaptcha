hCaptcha
=================

A spam protection field for SilverStripe using the hCaptcha service.


## Requirements
* SilverStripe 4.x
* [SilverStripe Spam Protection 3.x](https://github.com/silverstripe/silverstripe-spamprotection/)
* PHP CURL

## Installation
```
composer require 3dgoo/silverstripe-hcaptcha
```

After installing the module via composer we must set the spam protector to HCaptchaProtector through a config yml file. For example we can create a `app/\_config/spamprotection.yml` file with the following settings.
```yml
---
name: app-spamprotection
---
SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension:
  default_spam_protector: X3dgoo\HCaptcha\Forms\HCaptchaProtector

X3dgoo\HCaptcha\Forms\HCaptchaField:
  site_key: "YOUR_SITE_KEY"
  secret_key: "YOUR_SECRET_KEY"
```
