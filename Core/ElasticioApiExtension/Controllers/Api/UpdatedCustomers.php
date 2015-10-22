<?php

class Shopware_Controllers_Api_UpdatedCustomers extends Shopware_Controllers_Api_Rest
{
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('customer');
    }

    /**
     * GET /api/updatedCustomers/
     * GET /api/updatedCustomers?updatedSince=5345345343344
     */
    public function indexAction()
    {
        $limit  = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort   = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        $updatedSince = $this->Request()->getParam('updatedSince', 0);

        if (!$updatedSince) {
            // just query all customers
            $result = $this->resource->getList($offset, $limit, $filter, $sort);
        } else {
            // query only customers with ids from updated list
            $customerIds = $this->_getUpdatedCustomerIds($updatedSince, $offset, $limit);
            array_push($filter, array('property'   => 'id', 'value' => $customerIds));
            // set offset to 0
            $result = $this->resource->getList(0, $limit, $filter, $sort);
        }

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

    private function _getUpdatedCustomerIds($updatedSince, $offset, $limit)
    {
        $sql = "SELECT id FROM s_user "
             . "WHERE UNIX_TIMESTAMP(updatedOn) > :updatedSince "
             . "ORDER BY updatedOn ASC "
             . "LIMIT :offset,:limit ";

        $statement = Shopware()->Db()->prepare($sql);
        $statement->bindValue(':updatedSince', $updatedSince, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }
}