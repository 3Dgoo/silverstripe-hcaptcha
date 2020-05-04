<?php

namespace X3dgoo\HCaptcha\Forms;

use SilverStripe\SpamProtection\SpamProtector;

class HCaptchaProtector implements SpamProtector
{
    /**
     * Return the Field that we will use in this protector
     * @return string
     */
    public function getFormField($name = 'HCaptchaField', $title = 'Captcha', $value = null)
    {
        return HCaptchaField::create($name, $title);
    }

    /**
     * Not used by HCaptcha
     */
    public function setFieldMapping($fieldMapping)
    {
    }
}
