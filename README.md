# ProductDiscount
Getting a list of discounted products

Use
---

### Create a cart rule
![Общие параметры правила](https://github.com/Cleverscript/ProductDiscount/blob/main/cond_1.png)
![Выполняемые действия и условия применения](https://github.com/Cleverscript/ProductDiscount/blob/main/cond_2.png)

### Use the class to get product IDs and pass them to the component filter
```php
$products = ProductDiscount::getDiscounProducts();
$GLOBALS["arrFilter"] = Array("ID" => $products);

<? if(count($products)):?>
	<?$APPLICATION->IncludeComponent(
        "bitrix:catalog.section", 
        "",
        array(
        ...
        "FILTER_NAME" => "arrFilter",
        ...
		),
		false
	);?>
<?else: ?>
	There are no products to display.
<? endif; ?>	
```


