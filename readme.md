### SQL

#### Задание 1

Дана таблица ​items (​​id, category_id, name, price)​.
Напишите один запрос, без использования вложенных SELECT, который выбирает 3 товара с максимальной ценой из каждого category_id.

```sql
# Так вышло что первый вариант получился с подзапросом
SELECT *
FROM items i1
WHERE (SELECT COUNT(*) FROM items i2 WHERE i1.category_id = i2.category_id AND i2.price > i1.price) < 3
ORDER BY category_id, price DESC;

# И без подзапроса
SELECT i1.*
FROM items i1
INNER JOIN items i2 ON i1.category_id = i2.category_id
WHERE i2.price >= i1.price
GROUP BY i1.id
HAVING COUNT(i1.id) <= 3
ORDER BY i1.category_id, i1.price DESC;
```

#### Задание 2