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
        $withExternalId = $this->Request()->getParam('withExternalId', false);

        // query updated customer ids
        $updatedUsers = $this->_getUpdatedUsers(array(
            'updatedSince' => $updatedSince,
            'withExternalId' => $withExternalId,
            'limit' => $limit,
            'offset' => $offset
        ));

        // put customer ids to filter
        array_push($filter, array(
            'property' => 'id',
            'value' => array_keys($updatedUsers)
        ));

        // query customers
        $result = $this->resource->getList(0, $limit, $filter, $sort);

        // add "updatedOn" to each customer
        foreach ($result['data'] as &$customer) {
            $customer['updatedOn'] = intval($updatedUsers[$customer['id']]['updatedOn']);
        }

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

    private function _getUpdatedUsers($params)
    {
        if ($params['withExternalId']) {
            $join = " INNER JOIN s_user_attributes a "
                  . " ON (a.userID = u.id AND a.elasticio_external_id IS NOT NULL) ";
        }

        $sql = "SELECT u.id, UNIX_TIMESTAMP(u.updatedOn) as updatedOn "
             . "FROM s_user u "
             . $join
             . "WHERE UNIX_TIMESTAMP(u.updatedOn) >= :updatedSince "
             . "ORDER BY u.updatedOn ASC "
             . "LIMIT :offset, :limit ";

        $statement = Shopware()->Db()->prepare($sql);
        $statement->bindValue(':updatedSince', intval($params['updatedSince']), \PDO::PARAM_INT);
        $statement->bindValue(':offset', intval($params['offset']), \PDO::PARAM_INT);
        $statement->bindValue(':limit', intval($params['limit']), \PDO::PARAM_INT);
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