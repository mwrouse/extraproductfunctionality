# Extra Product Functionality
ThirtyBees module to add extra variables to a product

![Image of product tab](img/productTab.png)

## How To use
Once you install this module you will see a new tab in the product page (when editing a product).

In this tab you will be able to set different functionality on a product. Marking it as a service, forcing it to be marked as new, and a coming soon attribute.

Any variable set on there is designed to be used on your template to show different things and different views.

In your `product.tpl` file at the top add this:

`{hook h='actionModifyProductForExtraFunctionality' product=$product}`

This will modify `$product` to add the different variables (all are listed in the new tab on the product edit page).

You can then do things like

```html
{if isset($product->coming_soon) && $product->coming_soon == 1}
    <span class="product-label product-label-comingsoon">{l s='Coming Soon!'}</span>
{/if}
```
in your theme to add a product label for coming soon.

**It is always recommended to do an `isset` before accessing these variables** as this will prevent errors if the module is disabled.

## Product List
Getting this module to work on the products list was notably trickier than getting it to work on the product page. But, I've found a way.

At the top of your theme's `product-list-item.tpl` file (depending on your theme this might be different--but wherever for-loops through a list of products) add this:

```php
{capture name="newProduct"}
    {hook h='actionModifyProductForExtraFunctionality' product=$product capture=true}
{/capture}
{assign var="product" value=unserialize($smarty.capture.newProduct)}
```
This is all needed because the `$product` in this part is not a class like in the `product.tpl` file, it's an associative array.

For some reason something won't let you modify those from inside the module (like how the module can directly modify `$product` in the `product.tpl` file).

So we have to re-assign the `$product` variable after deserializing it.