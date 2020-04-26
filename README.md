# Order Recipe

Developed in Laravel with MySQL, API endpoints are created to have a system to manage inventory for ingredients

## Installation

Install composer from [here](https://getcomposer.org/download/).

## Run migration to import DB

```bash
php artisan migrate
```

## API endpoints

**Add Ingredient**

Endpoint

```bash
/api/ingredients
```
Request method
```bash
POST
```

Request sample (JSON)
```bash
{
  "name": "Potato",
  "measure": "kg",
  "supplier": "Alain"
}
```
Request validations

- Ingredient name `name` allows only alphabets and spaces and is required
- Measure `measure` allows only `g`, `kg`, `pieces` and is required
- Supplier `supplier` allows only alphabets and spaces and is required

**Ingredient listing**

Endpoint

```bash
/api/ingredients
```
Request method
```bash
GET
```

Request sample (JSON)
```bash
{
  "page": 1
}
```
Request helper
- Per page is defaulted to 10 and pass page number in `page` attribute

**Add Recipe**

Endpoint

```bash
/api/recipe
```
Request method
```bash
POST
```

Request sample (JSON)
```bash
{
  "ingredients": {
    "Tomato" : 2,
    "Potato" : 3
  },
  "name": "Pulav",
  "description": "Hyderabadi Style"
}
```
Request validations

- Ingredient list `ingredients` should be an array with key value pair, distinct and is required
- The key value pair above should have a valid `ingredient` as key and valid integer as `amount`
- Recipe name `name` allows only alphabets and spaces and is required
- Recipe description `description` is a free text and is required

**Recipe listing**

Endpoint

```bash
/api/recipe
```
Request method
```bash
GET
```

Request sample (JSON)
```bash
{
  "page": 1
}
```

Request helper
- Per page is defaulted to 10 and pass page number in `page` attribute

**Add Box**

Endpoint

```bash
/api/box
```
Request method
```bash
POST
```

Request sample (JSON)
```bash
{
  "recipe_id": [1,2],
  "delivery_date": "2020-04-28"
}
```
Request validations

- Recipe IDs `recipe_id` should be an array of valid IDs with a maximum of 4 and is required
- Delivery date `delivery_date` should be in format `Y-m-d` and it cannot be in the past, in next two days of date entered and is required. The requirement here was 48 hours but since `delivery_date` is estimated without time, we have considered it as 2 days instead

**Ingredient list to be ordered by the company**

Endpoint

```bash
/api/companyorder
```
Request method
```bash
GET
```

Request sample (JSON)
```bash
{
  "order_date": "2020-04-26",
  "delivery_date":"2020-04-28",
  "supplier" : "lulu"
}
```

Request helper
- Order date `order_date` should be in format `Y-m-d` and is required. The fetched results will contain ingredients from the ordered date entered until the next 7 days
- Delivery date `delivery_date` should be in format `Y-m-d` and is not mandatory
- Supplier `supplier` allows only alphabets and spaces and is not mandatory
- If the entered `supplier` is not a valid supplier, the system will ignore the filter and bring all results

## Thank You
- Thanks for giving me this wonderful opportunity to showcase my skill set
- I tried my level best to implement docker but it ran into multiple issues as i have Windows 10 Home edition
- All code explanations and logic are written alongside code