<?php

class Shopware_Controllers_Api_UpdatedCustomers extends Shopware_Controllers_Api_Rest
{
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('updatedCustomer');
    }

    /**
     * GET /api/updatedCustomers/
     */
    public function indexAction()
    {
        $sql = "SELECT id FROM s_user WHERE UNIX_TIMESTAMP(updatedOn) > 0 ";
        $statement = Shopware()->Db()->query($sql);
        $ids = $statement->fetchAll(\PDO::FETCH_COLUMN);
        error_log(print_r($ids, true));

        $limit  = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort   = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        array_push($filter, array(
            'property'   => 'id'
            'value'      => $ids
        ));

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }
}