<?php

class Shopware_Plugins_Core_ElasticioApiExtension_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    const COLUMN_PREFIX = 'elasticio';

    private $PLUGIN_API_CONTROLLERS = array(
        'ArticlePrices',
        'CustomersWithoutExternalId',
        'UpdatedCustomers',
        'CustomerGroupByKey',
        'Countries',
        'NewOrders',
        'OrdersByExternalId',
        'CustomersByExternalId'
    );

    public function getVersion()
    {
        return '1.0.0';
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
        $this->addUserColumns();
        return true;
    }

    public function uninstall()
    {
        try {
            // commented because we don't want to lose externalIds
            // when reinstall plugin
            // $this->removeAttributes();
        } catch (\Exception $e) {
            // noting to do here.
        }

        return true;
    }

    private function addUserColumns() {

        $sql = "SHOW COLUMNS FROM s_user LIKE 'updatedOn'";
        $updatedOnColumn = Shopware()->Db()->fetchRow($sql);

        if (empty($updatedOnColumn)) {
            $sql = "ALTER TABLE s_user ADD COLUMN updatedOn TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
            Shopware()->Db()->query($sql);
        }
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

        foreach ($this->PLUGIN_API_CONTROLLERS as $controllerName) {
            $this->subscribeEvent(
                "Enlight_Controller_Dispatcher_ControllerPath_Api_{$controllerName}",
                "onGetApiController{$controllerName}"
            );
        }
    }

    private function addAttributes() {
        $this->Application()->Models()->addAttribute(
            's_user_attributes',
            self::COLUMN_PREFIX,
            'external_id',
            'VARCHAR(255)',
            true,
            null
        );

        $this->Application()->Models()->addAttribute(
            's_order_attributes',
            self::COLUMN_PREFIX,
            'external_id',
            'VARCHAR(255)',
            true,
            null
        );

        $this->Application()->Models()->generateAttributeModels(array(
            's_user_attributes',
            's_order_attributes'
        ));
    }

    private function removeAttributes() {
        $this->Application()->Models()->removeAttribute(
            's_user_attributes',
            self::COLUMN_PREFIX,
            'external_id'
        );

        $this->Application()->Models()->removeAttribute(
            's_order_attributes',
            self::COLUMN_PREFIX,
            'external_id'
        );

        $this->Application()->Models()->generateAttributeModels(array(
            's_user_attributes',
            's_order_attributes'
        ));
    }

    public function onFrontStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\Components',
            $this->Path() . 'Components/'
        );
    }

    public function onGetApiControllerArticlePrices()
    {
        return $this->Path() . 'Controllers/Api/ArticlePrices.php';
    }

    public function onGetApiControllerCustomerGroupByKey()
    {
        return $this->Path() . 'Controllers/Api/CustomerGroupByKey.php';
    }

    public function onGetApiControllerCustomersWithoutExternalId()
    {
        return $this->Path() . 'Controllers/Api/CustomersWithoutExternalId.php';
    }

    public function onGetApiControllerUpdatedCustomers()
    {
        return $this->Path() . 'Controllers/Api/UpdatedCustomers.php';
    }

    public function onGetApiControllerCountries()
    {
        return $this->Path() . 'Controllers/Api/Countries.php';
    }

    public function onGetApiControllerNewOrders()
    {
        return $this->Path() . 'Controllers/Api/NewOrders.php';
    }

    public function onGetApiControllerOrdersByExternalId()
    {
        return $this->Path() . 'Controllers/Api/OrdersByExternalId.php';
    }

    public function onGetApiControllerCustomersByExternalId()
    {
        return $this->Path() . 'Controllers/Api/CustomersByExternalId.php';
    }
}
