<?php

namespace X3dgoo\HCaptcha\Tests;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\TextField;
use SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension;
use X3dgoo\HCaptcha\Forms\HCaptchaProtector;

class HCaptchaProtectorTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testCreateHCaptchaProtector()
    {
        $hCaptchaProtector = new HCaptchaProtector();

        $this->assertNotNull($hCaptchaProtector);
    }

    public function testEnableSpamProtection()
    {
        Config::modify()->set(
            FormSpamProtectionExtension::class,
            'default_spam_protector',
            HCaptchaProtector::class
        );

        $form = Form::create(Controller::create(), 'Form', FieldList::create(
            TextField::create('Title'),
            TextField::create('Comment')
        ), FieldList::create());

        $form->disableSecurityToken();

        $form = $form->enableSpamProtection();

        $this->assertNotNull($form->Fields()->fieldByName('Captcha'));
    }
}
