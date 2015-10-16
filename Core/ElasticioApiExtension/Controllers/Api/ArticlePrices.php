<?php

class Shopware_Controllers_Api_ArticlePrices extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ArticlePrice
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('subscriber');
    }

    /**
     * Get list of subscribers
     *
     * GET /api/articlePrices/
     */
    public function indexAction()
    {
        $limit  = $this->Request()->getParam('limit', 100);
        $offset = $this->Request()->getParam('start', 0);
        $sort   = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }
}
