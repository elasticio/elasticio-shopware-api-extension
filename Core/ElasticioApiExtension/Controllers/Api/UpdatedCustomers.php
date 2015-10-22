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
            // query only updated customer ids & data
            $updatedUsers = $this->_getUpdatedUsers($updatedSince, $offset, $limit);

            // put customer ids to filter
            array_push($filter, array('property' => 'id', 'value' => array_keys($updatedUsers)));

            // query customers using that filter
            $result = $this->resource->getList(0, $limit, $filter, $sort);

            // add updatedOn to each customer
            foreach ($result['data'] as &$customer) {
                $customer['updatedOn'] = $updatedUsers[$customer['id']]['updatedOn'];
            }
        }

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

    private function _getUpdatedUsers($updatedSince, $offset, $limit)
    {
        $sql = "SELECT id, updatedOn FROM s_user "
             . "WHERE UNIX_TIMESTAMP(updatedOn) > :updatedSince "
             . "ORDER BY updatedOn ASC "
             . "LIMIT :offset,:limit ";


        $statement = Shopware()->Db()->prepare($sql);
        $statement->bindValue(':updatedSince', $updatedSince, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $result = array();
        foreach ($data as $row) {
            $result[$row['id']] = array(
                'updatedOn' => $row['updatedOn']
            );
        }
        return $result;
    }
}