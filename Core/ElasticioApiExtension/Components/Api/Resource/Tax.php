<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;
use Shopware\Models\Tax\Tax as TaxModel;


class Tax extends Resource
{
    public function getRepository()
    {
        return $this->getManager()->getRepository('Shopware\Models\Tax\Tax');
    }

    /**
     * @param int $id
     * @return array|\Shopware\Models\Tax\Tax
     * @throws \Shopware\Components\Api\Exception\ParameterMissingException
     * @throws \Shopware\Components\Api\Exception\NotFoundException
     */
    public function getOne($id)
    {
        $this->checkPrivilege('read');

        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        $builder = $this->getRepository()
            ->createQueryBuilder('Tax')
            ->select('Tax')
            ->where('Tax.id = ?1')
            ->setParameter(1, $id);

        $user = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$user) {
            throw new ApiException\NotFoundException("Tax by id $id not found");
        }

        return $user;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList($offset = 0, $limit = 25, array $criteria = array(), array $orderBy = array())
    {
        $this->checkPrivilege('read');

        $builder = $this->getRepository()->createQueryBuilder('Tax');

        $builder->addFilter($criteria);
        $builder->addOrderBy($orderBy);
        $builder->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $builder->getQuery();

        $query->setHydrationMode($this->getResultMode());

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);

        //returns the total count of the query
        $totalResult = $paginator->count();

        //returns the User data
        $users = $paginator->getIterator()->getArrayCopy();

        return array('data' => $users, 'total' => $totalResult);
    }
}