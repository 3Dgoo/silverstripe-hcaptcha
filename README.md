# Silverstripe hCaptcha spam protection

[![Build Status](https://travis-ci.org/3dgoo/silverstripe-hcaptcha.svg?branch=master)](https://travis-ci.org/3dgoo/silverstripe-hcaptcha)
[![codecov.io](https://codecov.io/github/3dgoo/silverstripe-hcaptcha/coverage.svg?branch=master)](https://codecov.io/gh/3dgoo/silverstripe-hcaptcha?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/3dgoo/silverstripe-hcaptcha/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/3dgoo/silverstripe-hcaptcha/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/3dgoo/silverstripe-hcaptcha/v/stable)](https://packagist.org/packages/3dgoo/silverstripe-hcaptcha)
[![Total Downloads](https://poser.pugx.org/3dgoo/silverstripe-hcaptcha/downloads)](https://packagist.org/packages/3dgoo/silverstripe-hcaptcha)
[![Latest Unstable Version](https://poser.pugx.org/3dgoo/silverstripe-hcaptcha/v/unstable)](https://packagist.org/packages/3dgoo/silverstripe-hcaptcha)
[![License](https://poser.pugx.org/3dgoo/silverstripe-hcaptcha/license)](LICENSE)

A spam protection field for Silverstripe using the hCaptcha service.

<img src="docs/en/images/hcaptcha-screenshot.png" alt="hCaptcha screenshot" width="304" />

## Requirements

* Silverstripe Framework
* [Silverstripe Spam Protection](https://github.com/silverstripe/silverstripe-spamprotection/)
* [Guzzle](https://github.com/guzzle/guzzle/)

## Installation (with composer)

    $ composer require 3dgoo/silverstripe-hcaptcha

## Configuration

After installing the module via composer we must set the spam protector to HCaptchaProtector through a config yml file.

Create a `app/_config/spamprotection.yml` file and add the following details:

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

We generate our site key and secret key at https://www.hcaptcha.com/
