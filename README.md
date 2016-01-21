# Shopware REST API extenstions

* ``/api/ArticlePrices``: Create/Raad/Update article prices
* ``/api/CustomersWithoutExternalId``: Retrieve customers that have no ``external_id`` property set
* ``/api/OrdersWithoutExternalId``: Retrieve orders that have no ``external_id`` property set
* ``/api/UpdatedCustomers``: Retrieve customers updates after a given date
* ``/api/CustomerGroupByKey``: Retrieve customer groups by a given group key
* ``/api/Countries``: Retrieve all countries, retrieve country by id
* ``/api/NewOrders``: Retrieve orders created after a given date
* ``/api/OrdersByExternalId``: CRUD orders by a given ``external_id``
* ``/api/CustomersByExternalId``: CRUD customers by a given ``external_id``

# Building the plugin

To build the plugin archive execute the following command. The resulting zip can be uploaded into your Shopware instance.

````sh
build.sh
````
