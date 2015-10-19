<?php

class Shopware_Plugins_Core_ElasticioApiExtension_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

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
        $this->subscribeEvents();
        return true;
    }

    public function enable()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    /**
     * Registers all necessary events and hooks for ArticlePrices API
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
            'Enlight_Controller_Dispatcher_ControllerPath_Api_CustomerGroupByKey',
            'onGetCustomerGroupByKeyApiController'
        );
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
}
