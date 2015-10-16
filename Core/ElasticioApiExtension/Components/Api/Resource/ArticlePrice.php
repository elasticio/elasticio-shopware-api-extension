<?php

namespace Shopware\Components\Api\Resource;
use Shopware\Components\Api\Exception as ApiException;

class ArticlePrice extends Resource
{
    private function queryBuilder(){
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(array('price'));
        $builder->from('Shopware\Models\Article\Price', 'price');
        return $builder;
    }

    private function flatternResults($results){
        $addresses = array();
        foreach($results as $result) {
            $address = $result[0]; // address is in $result[0]
            $address["firstname"] = $result["firstName"];
            $address["lastname"] = $result["lastName"];
            $address["salutation"] = $result["salutation"];
            array_push($addresses, $result[0]);
        }
        return $addresses;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList($offset = 0, $limit = 100, array $criteria = array(), array $orderBy = array())
    {
        $this->checkPrivilege('read');

        $builder = $this->queryBuilder();
        $builder->addFilter($criteria);
        $builder->addOrderBy($orderBy);
        $builder->setFirstResult($offset)->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);

        //returns the total count of the query
        $totalResult = $paginator->count();

        //returns the Price data
        $results = $paginator->getIterator()->getArrayCopy();

        return array('data' => $results, 'offset' => $offset, 'limit' => $limit, 'total' => $totalResult);
    }
}
