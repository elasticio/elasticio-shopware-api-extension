<?php

class Shopware_Plugins_Core_ElasticioApiExtension_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    const COLUMN_PREFIX = 'elasticio';

    private function debugInfo($msg) {
        error_log($msg);
    }

    public function getVersion()
    {
        return '0.0.0';
    }

    public function getLabel()
    {
        return 'Elastic.io Shopware API Extension';
    }

    public function getCapabilities()
    {
        return array(
            'install' => true,
            'enable' => true,
            'update' => true
        );
    }

    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'supplier' => 'Elastic.io GMBH',
            'description' => 'Elastic.io Shopware API Extension',
            'support' => 'support@elastic.io',
            'link' => 'http://www.elastic.io/'
        );
    }

    public function install()
    {
        $this->addAttributes();
        $this->subscribeEvents();
        return true;
    }

    public function enable()
    {
        return true;
    }

    public function uninstall()
    {
        try {
            $this->removeAttributes();
        } catch (\Exception $e) {
            // noting to do here.
        }

        return true;
    }

    /**
     * Registers all necessary events and hooks
     */
    private function subscribeEvents()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Front_StartDispatch',
            'onFrontStartDispatch'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Api_ArticlePrices',
            'onGetArticlePricesApiController'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Api_CustomersWithoutExternalId',
            'onGetCustomersWithoutExternalIdApiController'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Api_CustomerGroupByKey',
            'onGetCustomerGroupByKeyApiController'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Api_Countries',
            'onGetCountriesApiController'
        );
    }

    private function addAttributes() {
        $this->Application()->Models()->addAttribute(
            's_user_attributes',
            self::COLUMN_PREFIX,
            'external_id',
            'INT(11)',
            true,
            null
        );

        $this->Application()->Models()->generateAttributeModels(array(
            's_user_attributes'
        ));
    }

    private function removeAttributes() {
        $this->Application()->Models()->removeAttribute(
            's_user_attributes',
            self::COLUMN_PREFIX,
            'external_id'
        );

        $this->Application()->Models()->generateAttributeModels(array(
            's_user_attributes'
        ));
    }

    public function onFrontStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\Components',
            $this->Path() . 'Components/'
        );
    }

    public function onGetArticlePricesApiController()
    {
        return $this->Path() . 'Controllers/Api/ArticlePrices.php';
    }

    public function onGetCustomerGroupByKeyApiController()
    {
        return $this->Path() . 'Controllers/Api/CustomerGroupByKey.php';
    }

    public function onGetCustomersWithoutExternalIdApiController()
    {
        return $this->Path() . 'Controllers/Api/CustomersWithoutExternalId.php';
    }

    public function onGetCountriesApiController()
    {
        return $this->Path() . 'Controllers/Api/Countries.php';
    }
}
