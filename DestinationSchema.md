# **Schema**

## **Destination Schema**

| Field           |  Type   | Null | Key | Default |     Extra      |
| ----------------| --------| ---- | --- | ------- | ---------------|
|     id          | int(11) | NO   | PRI | NULL    | auto_increment |
|  description    | text    | NO   |     | NULL    |                |
|  country_id     | int(11) | NO   | MUL | NULL    |                |
|  city_id        | int(11) | NO   | MUL | NULL    |                |
|  latitude       | float   | NO   |     | NULL    |                |
|  longitude      | float   | NO   |     | NULL    |                |
|  estimated_cost | float   | NO   |     | NULL    |                |

## **Country Schema**

| Field           |  Type   | Null | Key | Default |     Extra      |
| ----------------| --------| ---- | --- | ------- | ---------------|
|     id          | int(11) | NO   | PRI | NULL    | auto_increment |
|  name_ar        | text    | NO   |     | NULL    |                |
|  name_en        | text    | NO   |     | NULL    |                |

## **City Schema**

| Field           |  Type   | Null | Key | Default |     Extra      |
| ----------------| --------| ---- | --- | ------- | ---------------|
|     id          | int(11) | NO   | PRI | NULL    | auto_increment |
|  name_ar        | text    | NO   |     | NULL    |                |
|  name_en        | text    | NO   |     | NULL    |                |
|  country_id     | int(11) | NO   |     | NULL    |                |

## **Travel Interests Schema**

| Field           |  Type   | Null | Key | Default |     Extra      |
| ----------------| --------| ---- | --- | ------- | ---------------|
|     id          | int(11) | NO   | PRI | NULL    | auto_increment |
|  name_ar        | text    | NO   |     | NULL    |                |
|  name_en        | text    | NO   |     | NULL    |                |

## **Accommodations Schema**

| Field           |  Type   | Null | Key | Default |     Extra      |
| ----------------| --------| ---- | --- | ------- | ---------------|
|     id          | int(11) | NO   | PRI | NULL    | auto_increment |
|  name_ar        | text    | NO   |     | NULL    |                |
|  name_en        | text    | NO   |     | NULL    |                |
|  min_price      | float   | NO   |     | NULL    |                |
|  max_price      | float   | NO   |     | NULL    |                |
| destination_id  | int(11) | NO   | MUL | NULL    |                |

## **Transportation Schema**

| Field           |  Type   | Null | Key | Default |     Extra      |
| ----------------| --------| ---- | --- | ------- | ---------------|
|     id          | int(11) | NO   | PRI | NULL    | auto_increment |
|  name_ar        | text    | NO   |     | NULL    |                |
|  name_en        | text    | NO   |     | NULL    |                |
|  min_price      | float   | NO   |     | NULL    |                |
|  max_price      | float   | NO   |     | NULL    |                |
| destination_id  | int(11) | NO   | MUL | NULL    |                |

## **Place Schema**

| Field           |  Type   | Null | Key | Default |     Extra      |
| ----------------| --------| ---- | --- | ------- | ---------------|
|     id          | int(11) | NO   | PRI | NULL    | auto_increment |
|  name_ar        | text    | NO   |     | NULL    |                |
|  name_en        | text    | NO   |     | NULL    |                |
| destination_id  | int(11) | NO   | MUL | NULL    |                |

## **Destination Travel Interest Schema**

|       Field         |  Type   | Null | Key | Default |     Extra      |
| --------------------| --------| ---- | --- | ------- | ---------------|
|     id              | int(11) | NO   | PRI | NULL    | auto_increment |
|  destination_id     | int(11) | NO   | MUL | NULL    |                |
|  travel_interest_id | int(11) | NO   | MUL | NULL    |                |
