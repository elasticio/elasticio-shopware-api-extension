<?php

use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;

class Shopware_Plugins_Core_ElasticioApiExtension_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    const COLUMN_PREFIX = 'elasticio';

    private $PLUGIN_API_CONTROLLERS = array(
        'Tax',
        'Supplier',
        'ArticlePrices',
        'CustomersWithoutExternalId',
        'OrdersWithoutExternalId',
        'UpdatedCustomers',
        'CustomerGroupByKey',
        'Countries',
        'NewOrders',
        'OrdersByExternalId',
        'CustomersByExternalId',
        'ArticlesListId',
        'CustomersListId'
    );

    public function getVersion()
    {
        return '1.0.5';
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

    public function addAttribute($table, $prefix, $column, $type, $nullable = true, $default = null)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException('No table name passed');
        }
        if (strpos($table, '_attributes') === false) {
            throw new \InvalidArgumentException('The passed table name is no attribute table');
        }
        if (empty($prefix)) {
            throw new \InvalidArgumentException('No column prefix passed');
        }
        if (empty($column)) {
            throw new \InvalidArgumentException('No column name passed');
        }
        if (empty($type)) {
            throw new \InvalidArgumentException('No column type passed');
        }
        $type = $this->convertColumnType($type);
        $prefixedColumn = $prefix . '_' . $column;
        /** @var CrudService $crudService */
        $crudService = Shopware()->Container()->get('shopware_attribute.crud_service');
        $crudService->update($table, $prefixedColumn, $type, [], null, false, $default);
    }

    public function removeAttribute($table, $prefix, $column)
    {
        if (empty($table)) {
            throw new \InvalidArgumentException('No table name passed');
        }
        if (strpos($table, '_attributes') === false) {
            throw new \InvalidArgumentException('The passed table name is no attribute table');
        }
        if (empty($prefix)) {
            throw new \InvalidArgumentException('No column prefix passed');
        }
        if (empty($column)) {
            throw new \InvalidArgumentException('No column name passed');
        }
        $prefixedColumn = $prefix . '_' . $column;
        /** @var CrudService $crudService */
        $crudService = Shopware()->Container()->get('shopware_attribute.crud_service');
        $crudService->delete($table, $prefixedColumn, false);
    }

    private function addAttributes() {
        $this->addAttribute(
            's_user_attributes',
            self::COLUMN_PREFIX,
            'external_id',
            'VARCHAR(255)',
            true,
            null
        );

        $this->addAttribute(
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
        $this->removeAttribute(
            's_user_attributes',
            self::COLUMN_PREFIX,
            'external_id'
        );

        $this->removeAttribute(
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

    public function onGetApiControllerOrdersWithoutExternalId()
    {
        return $this->Path() . 'Controllers/Api/OrdersWithoutExternalId.php';
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
    
    public function onGetApiControllerTax()
    {
        return $this->Path() . 'Controllers/Api/Tax.php';
    }
    
    public function onGetApiControllerSupplier()
    {
        return $this->Path() . 'Controllers/Api/Supplier.php';
    }

    public function onGetApiControllerArticlesListId()
    {
        return $this->Path() . 'Controllers/Api/ArticlesListId.php';
    }

    public function onGetApiControllerCustomersListId()
    {
        return $this->Path() . 'Controllers/Api/CustomersListId.php';
    }

    private function convertColumnType($type)
    {
        switch (true) {
            case (bool) preg_match('#\b(char\b|varchar)\b#i', $type):
                $type = TypeMapping::TYPE_STRING;
                break;
            case (bool) preg_match('#\b(text|blob|array|simple_array|json_array|object|binary|guid)\b#i', $type):
                $type = TypeMapping::TYPE_TEXT;
                break;
            case (bool) preg_match('#\b(datetime|timestamp)\b#i', $type):
                $type = TypeMapping::TYPE_DATETIME;
                break;
            case (bool) preg_match('#\b(date|datetimetz)\b#i', $type):
                $type = TypeMapping::TYPE_DATE;
                break;
            case (bool) preg_match('#\b(int|integer|smallint|tinyint|mediumint|bigint)\b#i', $type):
                $type = TypeMapping::TYPE_INTEGER;
                break;
            case (bool) preg_match('#\b(float|double|decimal|dec|fixed|numeric)\b#i', $type):
                $type = TypeMapping::TYPE_FLOAT;
                break;
            case (bool) preg_match('#\b(bool|boolean)\b#i', $type):
                $type = TypeMapping::TYPE_BOOLEAN;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Column type "%s" cannot be converted.', $type));
        }
        return $type;
    }
}
