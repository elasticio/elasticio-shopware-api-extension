<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;
use Shopware\Components\Api\Resource\Customer as CustomerResource;

class CustomerByExternalId extends CustomerResource
{
    public function getOne($externalId)
    {
        $id = $this->_getCustomerIdByExternalId($externalId);
        return parent::getOne($id);
    }

    public function update($externalId, array $params)
    {
        $id = $this->_getCustomerIdByExternalId($externalId);
        return parent::update($id, $params);
    }

    public function delete($externalId)
    {
        $id = $this->_getCustomerIdByExternalId($externalId);
        return parent::delete($id, $params);
    }

    private function _getCustomerIdByExternalId($externalId)
    {
        if (empty($externalId)) {
            throw new ApiException\ParameterMissingException('External ID is required');
        }

        $repository = Shopware()->Models()->getRepository('Shopware\Models\Attribute\Customer');
        $attribute = $repository->findOneBy(array('elasticioExternalId' => $externalId));

        if (!$attribute) {
            throw new ApiException\NotFoundException("Customer by external ID $externalId not found");
        }

        return $attribute->getCustomerId();
    }
}