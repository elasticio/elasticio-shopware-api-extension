<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;
use Shopware\Models\Country\Country as CountryModel;
use Shopware\Components\Api\Resource\Order as OrderResource;

class OrderByExternalId extends OrderResource
{
    public function getOne($externalId)
    {
        $id = $this->_getOrderIdByExternalId($externalId);
        return parent::getOne($id);
    }

    public function update($externalId, array $params)
    {
        $id = $this->_getOrderIdByExternalId($externalId);
        return parent::update($id, $params);
    }

    public function delete($externalId)
    {
        $id = $this->_getOrderIdByExternalId($externalId);
        return parent::delete($id, $params);
    }

    private function _getOrderIdByExternalId($externalId)
    {
        if (empty($externalId)) {
            throw new ApiException\ParameterMissingException('External ID is required');
        }

        $repository = Shopware()->Models()->getRepository('Shopware\Models\Attribute\Order');
        $attribute = $repository->findOneBy(array('elasticioExternalId' => $externalId));

        if (!$attribute) {
            throw new ApiException\NotFoundException("Order by external ID $externalId not found");
        }

        return $attribute->getOrderId();
    }
}