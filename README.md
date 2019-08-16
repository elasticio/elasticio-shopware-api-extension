# Shopware REST API extenstions

* ``/api/ArticlePrices``: Create/Raad/Update article prices
* ``/api/CustomersWithoutExternalId``: Retrieve customers that have no ``external_id`` property set
* ``/api/OrdersWithoutExternalId``: Retrieve orders that have no ``external_id`` property set
* ``/api/UpdatedCustomers``: Retrieve customers updates after a given date
* ``/api/CustomerGroupByKey/${groupKey}``: Retrieve customer groups by a given group key
* ``/api/Countries``: Retrieve all countries, retrieve country by id
* ``/api/NewOrders``: Retrieve orders created after a given date
* ``/api/OrdersByExternalId``: CRUD orders by a given ``external_id``
* ``/api/CustomersByExternalId``: CRUD customers by a given ``external_id``

# Install the plugin
Download [ElasticioApiExtension.zip](ElasticioApiExtension.zip) file and install it on you Shopware instance using [Plugin Manager](http://en.community.shopware.com/_detail_1167.html).


# Building the plugin

To build the plugin clone this repo and execute the following command in root direcroty. The resulting `ElasticioApiExtension.zip` can be installed on your Shopware instance.

````sh
./build.sh
````
