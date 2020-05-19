<?php

namespace X3dgoo\HCaptcha\Tests;

use PHPUnit_Framework_Error;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Forms\RequiredFields;
use X3dgoo\HCaptcha\Forms\HCaptchaField;

class HCaptchaFieldTest extends FunctionalTest
{
    protected $usesDatabase = true;

    public function testCreateHCaptchaField()
    {
        $hCaptchaField = new HCaptchaField('HCaptchaField');

        $this->assertNotNull($hCaptchaField);
    }

    public function testFieldFailure()
    {
        $hCaptchaField = new HCaptchaField('HCaptchaField');

        $this->expectException(PHPUnit_Framework_Error::class);
        $this->expectExceptionMessage(
            'You must configure HCaptchaField.site_key and HCaptchaField.secret_key. ' .
                'You can retrieve these at https://hcaptcha.com'
        );

        $hCaptchaField->Field();
    }

    public function testField()
    {
        Config::modify()->set(HCaptchaField::class, 'site_key', '{site-key}');
        Config::modify()->set(HCaptchaField::class, 'secret_key', '{secret-key}');

        $hCaptchaField = new HCaptchaField('HCaptchaField');

        $field = $hCaptchaField->Field();

        $this->assertNotNull($field);
    }

    public function testValidateFailure()
    {
        Config::modify()->set(HCaptchaField::class, 'site_key', '{site-key}');
        Config::modify()->set(HCaptchaField::class, 'secret_key', '{secret-key}');

        $hCaptchaField = new HCaptchaField('HCaptchaField');

        $result = $hCaptchaField->validate(new RequiredFields());

        $this->assertFalse($result);
    }

    public function testSetSiteKey()
    {
        $hCaptchaField = new HCaptchaField('HCaptchaField');

        $hCaptchaField->setSiteKey('{new-site-key}');

        $this->assertEquals('{new-site-key}', $hCaptchaField->getSiteKey());
    }

    public function testSetSecretKey()
    {
        $hCaptchaField = new HCaptchaField('HCaptchaField');

        $hCaptchaField->setSecretKey('{new-secret-key}');
    }
}
