# wp-rest-polylang

## Description

This plugin adds the `pll_lang_name`, `pll_lang_locale`, `pll_lang_tag`, `pll_lang_code` and `pll_lang_rtl` attributes to WP REST api response for each Post and Page request for site running the Polylang plugin.

## Attributes

Example

```
{
  [...]
  pll_lang_name: "English",
  pll_lang_locale: "en_US",
  pll_lang_tag: "en-US",
  pll_lang_code: "en",
  pll_lang_rtl: false,
  [...]
}
```

### Translations
List of translations for page or post
```
{
  [...]
  "pll_translations": [
    {
      name: "English",
      locale: "en_US",
      tag: "en-US",
      code: "en",
      rtl: false,
      id: 26
    }
  ],
  [...]
}
```

### Credits

Fork of Original Plugin: https://github.com/maru3l/wp-rest-polylang