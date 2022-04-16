#mysql

##exists
t1表的所有数据都会取出

```
SELECT * FROM t1 WHERE EXISTS (SELECT 1 from t2)
```

只取t1表中，pattern存在于t2表的数据

```
SELECT * FROM t1 WHERE EXISTS (SELECT 1 from t2 where t1.pattern = t2.pattern)
```

获取所有未消费过的用户

```
SELECT * FROM customers where NOT EXISTS (select 1 from orders where orders.customerNumber = customers.customerNumber)
```

假设你想在谁在旧金山的办公室工作的员工的每一个电话分机添加的号码5，您可以在UPDATE语句中WHERE的子句使用EXISTS如下：


```
UPDATE employees
SET
    extension = CONCAT(extension, '1')
WHERE EXISTS( 
    SELECT 1 FROM offices WHERE 
        city = 'San Francisco'
        AND offices.officeCode = employees.officeCode
); 
```

使用以下INSERT语句将没有任何销售订单的客户插入customers_archive表中。

```
INSERT INTO customers_archive
SELECT * FROM customers
WHERE NOT EXISTS( SELECT
1
FROM
orders
WHERE
orders.customernumber = customers.customernumber);   
 ``` 

使用EXISTS运算符的查询比使用IN运算符的查询要快得多。

原因是EXISTS操作员基于“至少找到”原则工作。它返回true并在找到至少一个匹配行后停止扫描表。

另一方面，当IN运算符与子查询组合时，MySQL必须首先处理子查询，然后使用子查询的结果来处理整个查询。

一般的经验法则是，如果子查询包含大量数据，则EXISTS运算符可提供更好的性能。

但是，如果从子查询返回的结果集非常小，则使用IN运算符的查询将执行得更快。