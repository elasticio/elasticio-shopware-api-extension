<?php

class Shopware_Controllers_Api_Supplier extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\Supplier
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('supplier');
    }

    /**
     * Get list of supplier
     *
     * GET /api/supplier/
     */
    public function indexAction()
    {
        $limit = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

    /**
     * Get one supplier
     *
     * GET /api/supplier/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $user = $this->resource->getOne($id);

        $this->View()->assign('data', $user);
        $this->View()->assign('success', true);
    }
}