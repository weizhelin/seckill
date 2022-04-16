```
UPDATE T1, T2,
[INNER JOIN | LEFT JOIN] T1 ON T1.C1 = T2. C1
SET T1.C2 = T2.C2,
T2.C3 = expr
WHERE condition 
```


```
UPDATE t3 INNER JOIN t4 on t3.id = t4.id
SET t3.`name` = CONCAT('updated_',t3.`name`),t4.`name` = CONCAT('updated_',t4.`name`)
```