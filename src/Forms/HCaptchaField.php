<?php

namespace X3dgoo\HCaptcha\Forms;

use Psr\Log\LoggerInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\Validation\Validator;
use SilverStripe\i18n\i18n;
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
     * Captcha theme, currently options are light and dark
     * @var string
     * @default light
     */
    private static $default_theme = 'light';

    /**
     * Send remoteip to the hCaptcha siteverify request
     * @var boolean
     * @default false
     */
    private static $send_remote_ip = false;

    /**
     * Captcha theme, currently options are light and dark
     * @var string
     */
    private $_captchaTheme;

    /**
     * Captcha language code
     * @var string
     */
    private static $language_code;

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
        $this->_captchaTheme = self::config()->default_theme;
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

        $languageCode = self::config()->language_code ?? i18n::getData()->langFromLocale(i18n::get_locale());

        Requirements::javascript(
            'https://hcaptcha.com/1/api.js?hl=' . $languageCode
        );

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

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://hcaptcha.com/',
        ]);

        $response = $client->request(
            'POST',
            'siteverify',
            $this->getRequestData($hCaptchaResponse)
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
     * Generate the data for the hCaptcha server validation request
     * @param string $hCaptchaResponse
     * @return array Returns an array of query data for the hCaptcha server validation request
     */
    private function getRequestData($hCaptchaResponse)
    {
        $data = [
            'secret' => $this->getSecretKey(),
            'response' => rawurlencode($hCaptchaResponse),
        ];

        if (self::config()->send_remote_ip && $_SERVER['REMOTE_ADDR']) {
            $data['remoteip'] = rawurlencode($_SERVER['REMOTE_ADDR']);
        }

        return [
            'form_params' => $data,
        ];
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

    /**
     * Sets the theme for this captcha
     * @param string $value Theme to set it to, currently the api supports light and dark
     * @return NocaptchaField
     */
    public function setTheme($value)
    {
        $this->_captchaTheme = $value;

        return $this;
    }

    /**
     * Gets the theme for this captcha
     * @return string
     */
    public function getCaptchaTheme()
    {
        return $this->_captchaTheme;
    }
}
