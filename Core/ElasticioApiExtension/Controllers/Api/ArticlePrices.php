<?php

class Shopware_Controllers_Api_ArticlePrices extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\ArticlePrice
     */
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('articlePrice');
    }

    /**
     * Get list of subscribers
     *
     * GET /api/articlePrices/
     * GET /api/articlePrices?filter[articleId]=117&filter[customerGroupKey]=EN
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

    /**
     * Get one article price by id
     *
     * GET /api/articlePrices/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        $articlePrice = $this->resource->getOne($id);
        $this->View()->assign('data', $articlePrice);
        $this->View()->assign('success', true);
    }

    /**
     * Create new article price
     *
     * POST /api/articlePrices
     */
    public function postAction()
    {
        $articlePrice = $this->resource->create($this->Request()->getPost());
        $location = $this->apiBaseUrl . 'articlePrices/' . $articlePrice->getId();
        $data = array(
            'id'       => $articlePrice->getId(),
            'location' => $location
        );
        $this->View()->assign(array('success' => true, 'data' => $data));
        $this->Response()->setHeader('Location', $location);
    }

    /**
     * Update article price by id
     *
     * PUT /api/articlePrices/{id}
     */
    public function putAction()
    {
        $id = $this->Request()->getParam('id');
        $params = $this->Request()->getPost();
        $result = $this->resource->update($id, $params);
        $location = $this->apiBaseUrl . 'articlePrices/' . $result->getId();
        $data = array(
            'id'       => $result->getId(),
            'location' => $location
        );
        $this->View()->assign(array('success' => true, 'data' => $data));
    }
}
