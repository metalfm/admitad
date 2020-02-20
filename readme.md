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

Получить все года рождений пользователей с указанием сколько мужчин и женщин родилось в этот год.
```sql
SELECT YEAR(birthdate)             AS year,
       SUM(IF('F' = gender, 1, 0)) AS female,
       SUM(IF('M' = gender, 1, 0)) AS male,
       SUM(IFNULL(gender, 1))      AS undetermined
FROM users
GROUP BY year
ORDER BY year;
```

^ выбирающий всех НЕ заблокированных пользователей
```sql
SELECT YEAR(birthdate)             AS year,
       SUM(IF('F' = gender, 1, 0)) AS female,
       SUM(IF('M' = gender, 1, 0)) AS male,
       SUM(IFNULL(gender, 1))      AS undetermined
FROM users
LEFT JOIN users_banned ub on users.id = ub.user_id
WHERE ub.user_id IS NULL
GROUP BY year
ORDER BY year;
```


## Install, build and run
- `cp .env.dist .env`
- `brew install havoc-io/mutagen/mutagen`
- add alias to ~/.bash_profile
```
alias dc="docker-compose"
alias dc-dev='docker-compose  -f `pwd`/docker-compose.dev.yml '
alias dc-prod='docker-compose -f `pwd`/docker-compose.prod.yml '
alias dc-exterminatus='docker stop $(docker ps -a -q) > /dev/null 2>&1; docker rm $(docker ps -a -q) > /dev/null 2>&1; docker rmi $(docker images -f "dangling=true" -q) > /dev/null 2>&1; echo 🔥'
```
- install [docker](https://hub.docker.com/editions/community/docker-ce-desktop-mac)
- `make build`
- `make start`
- `make stop`
