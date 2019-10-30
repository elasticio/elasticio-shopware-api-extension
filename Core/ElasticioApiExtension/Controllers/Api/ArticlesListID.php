<?php

class Shopware_Controllers_Api_ArticlesListID extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\Article
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('articlesListId');
    }

    /**
     * Get list ID of articles
     *
     * GET /api/articlesListID/
     */
    public function indexAction()
    {
        $limit  = $this->Request()->getParam('limit', 1000000);
        $offset = $this->Request()->getParam('start', 0);
        $sort   = $this->Request()->getParam('sort', array());
        $filter = $this->Request()->getParam('filter', array());

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign($result);
        $this->View()->assign('success', true);
    }

}