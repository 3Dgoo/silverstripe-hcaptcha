<?php

namespace X3dgoo\HCaptcha\Forms;

use Psr\Log\LoggerInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\Validator;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;

class HCaptchaField extends FormField
{
    /**
     * HCaptcha Site Key
     * @config HCaptchaField.site_key
     */
    private static $site_key;

    /**
     * HCaptcha Secret Key
     * @config HCaptchaField.secret_key
     */
    private static $secret_key;

    /**
     * HCaptcha Site Key
     * Configurable via Injector config
     */
    protected $_siteKey;

    /**
     * HCaptcha Site Key
     * Configurable via Injector config
     */
    protected $_secretKey;

    /**
     * Creates a new HCaptcha field.
     * @param string $name The internal field name, passed to forms.
     * @param string $title The human-readable field label.
     * @param mixed $value The value of the field (unused)
     */
    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        $this->title = $title;
    }

    /**
     * Adds in the requirements for the field
     * @param array $properties Array of properties for the form element (not used)
     * @return DBHTMLText Rendered field template
     */
    public function Field($properties = [])
    {
        $siteKey = $this->getSiteKey();
        $secretKey = $this->getSecretKey();

        if (empty($siteKey) || empty($secretKey)) {
            user_error(
                'You must configure HCaptchaField.site_key and HCaptchaField.secret_key. ' .
                    'You can retrieve these at https://hcaptcha.com',
                E_USER_ERROR
            );
        }

        Requirements::javascript('https://hcaptcha.com/1/api.js');

        return parent::Field($properties);
    }

    /**
     * Validates the captcha against the hCaptcha API
     * @param Validator $validator Validator to send errors to
     * @return bool Returns boolean true if valid false if not
     */
    public function validate($validator)
    {
        $valid = $this->processCaptcha();

        if (!$valid) {
            $validator->validationError(
                $this->name,
                _t(
                    'X3dgoo\\HCaptcha\\Forms\\HCaptchaField.EMPTY',
                    'Please answer the captcha. If you do not see the captcha please enable Javascript'
                ),
                'validation'
            );
        }

        return $valid;
    }


    /**
     * Validates the captcha against the hCaptcha API
     * @return bool Returns boolean true if valid false if not
     */
    private function processCaptcha()
    {
        $hCaptchaResponse = Controller::curr()->getRequest()->requestVar('h-captcha-response');

        if (!isset($hCaptchaResponse) || !$hCaptchaResponse) {
            return false;
        }

        $secretKey = $this->getSecretKey();

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://hcaptcha.com/',
        ]);

        $response = $client->request(
            'GET',
            'siteverify',
            [
                'query' => [
                    'secret' => $secretKey,
                    'response' => rawurlencode($hCaptchaResponse),
                    'remoteip' => rawurlencode($_SERVER['REMOTE_ADDR']),
                ],
            ]
        );

        $response = json_decode($response->getBody(), true);

        if (!is_array($response)) {
            $logger = Injector::inst()->get(LoggerInterface::class);
            $logger->error(
                'Captcha validation failed as request was not successful.'
            );

            return false;
        }

        if (array_key_exists('success', $response) && $response['success'] === false) {
            return false;
        }

        return true;
    }

    /**
     * Gets the site key configured via HCaptchaField.site_key this is used in the template
     * @return string
     */
    public function getSiteKey()
    {
        return $this->_siteKey ? $this->_siteKey : self::config()->site_key;
    }

    /**
     * Gets the secret key configured via HCaptchaField.secret_key
     * @return string
     */
    private function getSecretKey()
    {
        return $this->_secretKey ? $this->_secretKey : self::config()->secret_key;
    }

    /**
     * Setter for _siteKey to allow injector config to override the value
     * @param string $key
     */
    public function setSiteKey($key)
    {
        $this->_siteKey = $key;
    }

    /**
     * Setter for _secretKey to allow injector config to override the value
     * @param string $key
     */
    public function setSecretKey($key)
    {
        $this->_secretKey = $key;
    }

    /**
     * Gets the form's id
     * @return string
     */
    public function getFormID()
    {
        return ($this->form ? $this->getTemplateHelper()->generateFormID($this->form) : null);
    }
}
