<?php

use Shopware\Components\Api\Exception as ApiException;

class Shopware_Controllers_Api_OrdersByExternalId extends Shopware_Controllers_Api_Orders
{
    protected $resource = null;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('orderByExternalId');
    }
}
