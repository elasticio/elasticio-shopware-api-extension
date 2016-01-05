<?php

namespace Shopware\Components\Api\Resource;
use Shopware\Components\Api\Exception as ApiException;

class OrderWithoutExternalId extends Resource
{
    public function getList($offset = 0, $limit = 1000, array $criteria = array(), array $orderBy = array())
    {
        $this->checkPrivilege('read');

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(array('o'))
            ->from('Shopware\Models\Order\Order', 'o')
            ->leftJoin('o.attribute', 'attribute')
            ->where('attribute.elasticioExternalId IS NULL');

        $builder->addFilter($criteria);
        $builder->addOrderBy($orderBy);
        $builder->setFirstResult($offset)->setMaxResults($limit);

        $query = $builder->getQuery();

        $query->setHydrationMode($this->getResultMode());

        $paginator = $this->getManager()->createPaginator($query);

        //returns the total count of the query
        $totalResult = $paginator->count();

        //returns the customer data
        $orders = $paginator->getIterator()->getArrayCopy();

        return array('data' => $orders, 'total' => $totalResult);
    }
}