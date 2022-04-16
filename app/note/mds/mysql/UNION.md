union查询，要求字段数一样，字段名可以不以样，输出结果的字段名以第一条select语句为准。
重复的数据，只显示一条，仅保留不同的行。
如果要对union的结果进行排序，请ORDER BY在最后一个SELECT语句中使用一个子句，

```
SELECT id,pattern from t1
UNION
SELECT id,pattern from t2
UNION 
SELECT id,`name` FROM t3 order by id DESC LIMIT 3
```


MySQL还为您提供了基于列位置使用ORDER BY子句对结果集进行排序的替代选项，如下所示：

```SELECT
concat(firstName,' ',lastName) fullname
FROM
employees
UNION SELECT
concat(contactFirstName,' ',contactLastName)
FROM
customers
ORDER BY 1;
```

ORDER BY 后面使用的数字代着SELECT 字段的顺序序号，从1开始（上面实例中的1代表 fullname 字段）