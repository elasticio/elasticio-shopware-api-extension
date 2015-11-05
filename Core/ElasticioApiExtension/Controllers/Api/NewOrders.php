<?php

class Shopware_Controllers_Api_NewOrders extends Shopware_Controllers_Api_Rest
{
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('order');
    }

    /**
     * GET /api/newOrders/
     * GET /api/newOrders/?createdSince={UNIX_TIMESTAMP}
     */
    public function indexAction()
    {
        $limit  = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort   = $this->Request()->getParam('sort', []);
        $filter = $this->Request()->getParam('filter', []);
        $createdSince = $this->Request()->getParam('createdSince', 0);

        $newOrderIds = $this->_getNewOrderIds($createdSince, $offset, $limit);
        $total = $this->_getNewOrdersCount($createdSince);

        // put order ids to filter
        array_push($filter, [
            'property' => 'id',
            'value' => $newOrderIds
        ]);

        // query customers
        $result = $this->resource->getList(0, $limit, $filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('total', $total);
        $this->View()->assign('success', true);
    }

    private function _getNewOrderIds($createdSince, $offset, $limit)
    {
        $sql = [
            "SELECT o.id",
            "FROM s_order o",
            "WHERE o.ordertime > FROM_UNIXTIME(:createdSince)",
            "LIMIT :offset, :limit"
        ];

        $statement = Shopware()->Db()->prepare(join(' ', $sql));
        $statement->bindValue(':createdSince', intval($createdSince), \PDO::PARAM_INT);
        $statement->bindValue(':offset', intval($offset), \PDO::PARAM_INT);
        $statement->bindValue(':limit', intval($limit), \PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($data as $row) {
            $result[] = $row['id'];
        }
        return $result;
    }

    private function _getNewOrdersCount($createdSince)
    {
        $sql = [
            "SELECT COUNT(*) AS count",
            "FROM s_order o",
            "WHERE o.ordertime > FROM_UNIXTIME(:createdSince)"
        ];

        $statement = Shopware()->Db()->prepare(join(' ', $sql));
        $statement->bindValue(':createdSince', intval($createdSince), \PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetchObject();

        return intval($data->count);
    }
}