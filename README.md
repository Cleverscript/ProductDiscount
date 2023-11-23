# ProductDiscount
Getting a list of discounted products

Used

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


