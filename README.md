# Direct Energy

## Cybersecurity Filter class

A Filter class that will filter data to prevent against XSS attacks.

It makes use of a vendor library to do complex filtering.

All vendor libraries must implement the ExternalLibInterface interface to be able to use it.

In order to use the static facade one must configure the proper value in the config file so that the static facade knows how to satisfy the dependency needed.

To load it with composer:

```
{
  "require": {
    "de/csfilter": "^2.0.0"
  },
  "repositories": [
    {
      "type" : "vcs",
      "url" : "git@198.90.23.183:eandreakis/CSFilter.git"
    }
  ]
}
```

Although the library provides an adapter for HTMLPurifier it does not load it by default.

If you need HTMLPurifier to work you *MUST* include it to your composer file.

## How to use it?

We can use the library either by creating an instance of the Filter class or by using the static facade.

Please note that although the examples show the use of the HTMLPurifier external library, we can use whatever library we want by creating an adapter that implements the DE\CSFilter\ExternalLib\ExternalLibInterface.

### Std class

```
use DE\CSFilter\Filter;
use DE\CSFilter\ExternalLib\HTMLPurifierExternalLib;

//Setting up the filter
$filter = Filter::getInstance();
$externalLibAdapter = new HTMLPurifierExternalLib();
$filter->setExternalLib($externalLibAdapter);

//Use it
$cleanNumber = $filter->filterInt('5sdjkhfdkfh');
$cleanString = $filter->filterString('<div onClick="alert(\'Hello World\');"><strong>Valid string</strong> to http://example.com</div>');
$cleanNumber2 = $filter->filter('76pdnbnrgu rijrf', DE\CSFilter\Filter::TYPE_INTEGER);
```

### Static facade

```
use DE\CSFilter\SFilter;
use DE\CSFilter\ExternalLib\HTMLPurifierExternalLib;

//Set the static object
$externalLibAdapter = new HTMLPurifierExternalLib();
SFilter::setFilter($externalLibAdapter);

//Use it
$cleanNumber = SFilter::filterInt('5sdjkhfdkfh');
$cleanString = SFilter::filterString('<div onClick="alert(\'Hello World\');"><strong>Valid string</strong> to http://example.com</div>');
$cleanNumber2 =SFilter::filter('76pdnbnrgu rijrf', DE\CSFilter\Filter::TYPE_INTEGER);
```