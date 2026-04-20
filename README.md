# Sales Channel Specific Content for Shopware 6

`Sales Channel Specific Content` is a Shopware 6 plugin that allows product content to be customized per sales channel.

The plugin adds a custom `Content` tab to the product detail page in the Shopware Administration. From that tab, shop managers can select a sales channel and maintain alternative product content for that specific channel. On the storefront, the plugin loads the matching record for the current sales channel and applies the configured product data.

## Features

- Adds a `Content` tab to the Shopware product detail administration view.
- Stores custom product content per product and sales channel.
- Replaces selected product fields on storefront product pages when sales-channel-specific content exists.
- Supports HTML content for rich descriptions and additional information.
- Adds custom long description, product features, and "What's included" content to the storefront product description tab.
- Supports sales-channel-specific SEO fields.
- Keeps default Shopware product content as fallback when no custom content exists for the active sales channel.

## Editable Fields

The `Content` tab currently supports these fields:

| Field | Purpose | Storefront behavior |
| --- | --- | --- |
| Sales Channel | Selects which sales channel the content belongs to. | Used to decide which custom content record applies. |
| Product Name | Alternative product name for the selected sales channel. | Replaces the product name on the storefront for that sales channel. |
| Short Description | Alternative short/default product description. | Replaces Shopware's normal product description text. |
| Long Description | Extra detailed description for the selected sales channel. | Rendered below the normal description in the product description tab. |
| Product Features | Feature content for the selected sales channel. | Rendered below the long description. |
| What's Included | Included-items content for the selected sales channel. | Rendered below product features. |
| SEO Title | Alternative SEO title. | Applied to product metadata/title behavior. |
| SEO Keywords | Alternative SEO keywords. | Stored and mapped to product translated metadata. |
| SEO Description | Alternative SEO description. | Applied to product metadata behavior. |

<img width="517" height="758" alt="salesChContext" src="https://github.com/user-attachments/assets/b464cb0c-46c1-4432-95df-9c1b1e4cda2a" />

## Administration Usage

1. Go to `Catalogues > Products`.
2. Open the product you want to customize.
3. Open the `Content` tab.
4. Select the target sales channel.
5. Enter the content for that product and sales channel.
6. Click `Save`.
7. Open the product in the selected storefront sales channel to verify the result.

Each saved record belongs to one product and one sales channel. Changing the selected sales channel in the tab loads a different record.

<img width="1468" height="758" alt="Screenshot 2026-04-20 at 10 47 10" src="https://github.com/user-attachments/assets/f847c326-6676-4962-acce-f038a67442f4" />

---

<img width="1468" height="758" alt="Screenshot 2026-04-17 at 17 01 30" src="https://github.com/user-attachments/assets/5405d830-260b-4626-9ad7-e9e4786e99e6" />


## Installation

Install the plugin package with Composer:

```bash
composer require solution25/sales-channel-specific-content
```


## Troubleshooting

### The `Content` tab is not visible

Run:

```bash
bin/console plugin:refresh
bin/console plugin:install --activate SalesChannelSpecificContent
bin/build-administration.sh
bin/console cache:clear
```

## Compatibility

- Shopware 6.4, 6.5, 6.6, 6.7

## Author

Developed and maintained by [solution25](https://solution25.com/).

## License

MIT License
