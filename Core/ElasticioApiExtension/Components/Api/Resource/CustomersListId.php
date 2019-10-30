<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;
use Shopware\Models\Customer\Customer as CustomerModel;

class CustomersListId extends Resource
{
    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Customer\Customer');
    }
    
    /**
     * @param int $offset
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList($offset = 0, $limit = 1000000, array $criteria = array(), array $orderBy = array())
    {
        $this->checkPrivilege('read');

        $builder = $this->getRepository()->createQueryBuilder('Customer');

        $builder->select('Customer.number');
        $builder->addFilter($criteria);
        $builder->addOrderBy($orderBy);
        $builder->setFirstResult($offset)->setMaxResults($limit);

        $query = $builder->getQuery();

        $query->setHydrationMode($this->getResultMode());

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $paginator->setUseOutputWalkers(false);

        //returns the total count of the query
        $totalResult = $paginator->count();
        
        //returns the customer data
        $customers = $paginator->getIterator()->getArrayCopy();

        return array('data' => $customers, 'total' => $totalResult);
    }
}