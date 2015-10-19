<?php

use Shopware\Components\Api\Exception as ApiException;

class Shopware_Controllers_Api_CustomerGroupByKey extends Shopware_Controllers_Api_CustomerGroups
{
    /**
     * Get one customergroup by key
     *
     * GET /api/customergroupbykey/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        $result = $this->_getCustomerGroupByKey($id);
        $this->View()->assign('data', $result);
        $this->View()->assign('success', true);
    }

    /**
     * @param  int $id
     * @return array|\Shopware\Models\Customer\Group
     * @throws \Shopware\Components\Api\Exception\ParameterMissingException
     * @throws \Shopware\Components\Api\Exception\NotFoundException
     */
    private function _getCustomerGroupByKey($key)
    {
        $customerGroupResource = $this->resource;

        $customerGroupResource->checkPrivilege('read');
        if (empty($key)) {
            throw new ApiException\ParameterMissingException();
        }

        $builder = $customerGroupResource->getRepository()->createQueryBuilder('customerGroup')
                ->select('customerGroup', 'd')
                ->leftJoin('customerGroup.discounts', 'd')
                ->where('customerGroup.key = :key')
                ->setParameter(':key', $key);

        $query = $builder->getQuery();
        $query->setHydrationMode($customerGroupResource->getResultMode());

        /** @var $category \Shopware\Models\Customer\Group*/
        $result = $query->getOneOrNullResult($customerGroupResource->getResultMode());

        if (!$result) {
            throw new ApiException\NotFoundException("CustomerGroup by key $key not found");
        }

        return $result;
    }
}
