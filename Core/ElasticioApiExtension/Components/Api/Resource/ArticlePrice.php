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

    /**
     * @param int $id
     * @return array|\Shopware\Models\Article\Price
     * @throws \Shopware\Components\Api\Exception\ParameterMissingException
     * @throws \Shopware\Components\Api\Exception\NotFoundException
     */
    public function getOne($id)
    {
        $this->checkPrivilege('read');
        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        $builder = $this->queryBuilder();
        $builder->where('price.id = ?1');
        $builder->setParameter(1, $id);

        /** @var $articlePrice \Shopware\Models\Article\Price */
        $articlePrice = $builder->getQuery()->getOneOrNullResult($this->getResultMode());
        if (!$articlePrice) {
            throw new ApiException\NotFoundException("Article price by id $id not found");
        }
        return $articlePrice;
    }

    /**
     * @param  array $params
     * @return \Shopware\Models\Article\Price
     * @throws \Shopware\Components\Api\Exception\ValidationException
     * @throws \Exception
     */
    public function create(array $params)
    {
        $this->checkPrivilege('create');
        $params = $this->prepareArticlePriceData($params);

        $articlePrice = new \Shopware\Models\Article\Price();
        $articlePrice->fromArray($params);

        $violations = $this->getManager()->validate($articlePrice);
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->getManager()->persist($articlePrice);
        $this->flush();
        return $result;
    }

    /**
    * @param  int $id
    * @param  array $params
    * @return \Shopware\Models\Article\Price
    * @throws \Shopware\Components\Api\Exception\ValidationException
    * @throws \Shopware\Components\Api\Exception\NotFoundException
    * @throws \Shopware\Components\Api\Exception\ParameterMissingException
    * @throws \Shopware\Components\Api\Exception\CustomValidationException
    */
    public function update($id, array $params)
    {
        $this->checkPrivilege('update');
        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        /** @var $result \Shopware\Models\Article\Price */
        $articlePrice = $this->getRepository()->find($id);
        if (!$articlePrice) {
            throw new ApiException\NotFoundException("ArticlePrice by id $id not found");
        }

        $params = $this->prepareArticlePriceData($params, $articlePrice);
        $articlePrice->fromArray($params);

        $violations = $this->getManager()->validate($articlePrice);
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->flush();
        return $articlePrice;
    }

    /**
     * @param array $params
     * @param \Shopware\Models\Article\Price $articlePrice
     * @throws \Shopware\Components\Api\Exception\CustomValidationException
     * @return mixed
     */
    private function prepareArticlePriceData($params, $articlePrice = null)
    {
        $defaults = array(
            'from' => 1
        );

        if ($articlePrice === null) {

            if (empty($params['articleId'])) {
                throw new ApiException\CustomValidationException(sprintf("Parameter '%s' is missing", 'articleId'));
            }

            if (empty($params['customerGroupKey'])) {
                throw new ApiException\CustomValidationException(sprintf("Parameter '%s' is missing", 'customerGroupKey'));
            }

            if (!isset($params['from'])) {
                $params['from'] = $defaults['from'];
            }
        }

        return $params;
    }
}
