# Security Filter class

A Filter class that will filter data to prevent against XSS attacks.

It makes use of a third party library to do complex filtering.

All third party libraries must implement the FilteringLibAdapterInterface interface to be able to use it.

To load it with composer:

```
{
  "require": {
    "de/csfilter": "^3.0.0"
  },
  "repositories": [
    {
      "type" : "vcs",
      "url" : "git@github.com:spiro79/CSFilter.git"
    }
  ]
}
```

Although the library provides an adapter for HTMLPurifier it does not load it by default.

If you need HTMLPurifier to work you **MUST** include it to your composer file.

## How to use it?

We can use the library by creating an instance of the Filter class.

**Please note** that although the examples show the use of the *HTMLPurifier* filtering library, we can use whatever library we want by creating an adapter that implements the Security\XSSFilter\FilteringLib\FilteringLibInterface.

## Filter types

As per the FilterInterface definition file:

- TYPE_BOOLEAN
- TYPE_INTEGER
- TYPE_NUMBER
- TYPE_FLOAT
- TYPE_EMAIL
- TYPE_STRING
- TYPE_RICH
- TYPE_CUSTOM

### Example

You can also refer to the example file:

*codeExample.php*

```
use Security\XSSFilter\Filter;
use Security\XSSFilter\FilteringLib\HTMLPurifierFilteringLibAdapter;

//Setting up the filter
$filter = new Filter();
$filteringLibAdapter = new HTMLPurifierFilteringLibAdapter();
$filter->setFilteringLibAdapter($filteringLibAdapter);

//The short form:
//$filter = (new Filter())->setFilteringLibAdapter(new HTMLPurifierFilteringLibAdapter());

//Use it
$cleanNumber = $filter->filterInt('5sdjkhfdkfh');
$cleanString = $filter->filterString('<div onClick="alert(\'Hello World\');"><strong>Valid string</strong> to http://example.com</div>');
$cleanNumber2 = $filter->filter('76pdnbnrgu rijrf', Filter::TYPE_INTEGER);
```