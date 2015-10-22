<?php

class Shopware_Controllers_Api_UpdatedCustomers extends Shopware_Controllers_Api_Rest
{
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('updatedCustomer');
    }

    private function findCustomerIds($updatedSince, $offset, $limit)
    {
        $sql = "SELECT id FROM s_user "
             . "WHERE UNIX_TIMESTAMP(updatedOn) > {$updatedSince} "
             . "ORDER BY updatedOn ASC " // from oldest to newest
             . "LIMIT {$offset}, {$limit} ";

        $statement = Shopware()->Db()->query($sql);
        $ids = $statement->fetchAll(\PDO::FETCH_COLUMN);
        return $ids;
    }

    /**
     * GET /api/updatedCustomers/
     * GET /api/updatedCustomers?since=5345345343344
     */
    public function indexAction()
    {
        $limit  = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort   = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        $updatedSince = $this->Request()->getParam('updatedSince', 0);

        if (!$updatedSince) {
            $result = $this->resource->getList($offset, $limit, $filter, $sort);
        } else {
            $customerIds = $this->findCustomerIds($updatedSince, $offset, $limit);
            array_push($filter, array('property'   => 'id', 'value' => $customerIds));
            $result = $this->resource->getList(0, $limit, $filter, $sort);
        }

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }
}