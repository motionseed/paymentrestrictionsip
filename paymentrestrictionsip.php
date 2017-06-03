<?php
/**
 *  Payment Methods Restrictions by IP
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2017 motionSeed. All Rights Reserved.
 *  @license   https://www.motionseed.com/en/free-license-agreement.html
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('MotionSeedModule')) {
    include_once(dirname(__FILE__) . '/helpers/motionseed-module/MotionSeedModule.php');
}

class PaymentRestrictionsIP extends MotionSeedModule
{

    public function __construct()
    {
        $this->name = 'paymentrestrictionsip';
        $this->tab = 'administration';
        $this->version = '1.7.1';
        $this->author = 'motionSeed';
        $this->need_instance = 0;
        $this->ps_versions_compliancy['min'] = '1.6.0.0';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Payment Methods Restrictions by IP');
        $this->description = $this->l('Use the country associated to customer IP to display payment methods');

        $this->error = false;
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = '2056c754e3ebe60b4d623950804207b9';
    }
    
    public function registerHooks()
    {
        return parent::registerHooks() && $this->registerHook('displayPaymentTop');
}

    public function hookDisplayPaymentTop()
    {
        include_once(_PS_GEOIP_DIR_.'geoipcity.inc');
            
        $gi = geoip_open(realpath(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_), GEOIP_STANDARD);
        $record = geoip_record_by_addr($gi, Tools::getRemoteAddr());
        $id_country = null;
        
        if ($record) {
            $id_country = Country::getByIso($record->country_code);
        }

        // Do not return payment methods if the country cannot be guessed
        if (!$id_country) {
            $id_country = -1;
        }
        
        $this->context->country->id = (int)$id_country;
    }
    
}
