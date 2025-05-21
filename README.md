# Sales Channel Specific Content for Shopware 6

This plugin allows you to define **sales channel specific content** for each product in your Shopware 6 store. It adds a new **"Content" tab** on the product detail page within the Shopware administration panel. From this tab, you can configure how product details (descriptions, SEO data, features, etc.) appear per sales channel.

## 🛠 Features

- Adds a **Content** tab in the product detail admin view.
- Enables different product data for each **Sales Channel**.
- Editable content includes:
    - Product name & URL
    - Long & short description
    - SEO title, keywords, and description
    - What’s included
    - Product features
    - Wholesale & retail prices

> These fields are configurable individually for each sales channel you select.

## 📦 Installation

Install via composer:

```bash
composer require solution25/sales-channel-specific-content
```

Then install & activate the plugin via CLI:

```bash
bin/console plugin:install --activate SalesChannelSpecificContentShopware6
bin/console cache:clear
```

Alternatively, you can upload it via the admin panel if you clone/download it manually.

## ✅ Compatibility

- Shopware 6.4+
- Tested up to Shopware 6.6

## 🔧 Configuration

1. Go to **Catalogues > Products** in the admin panel.
2. Open a product and navigate to the **Content** tab.
3. Choose the sales channel from the dropdown.
4. Fill in or override product data fields as needed.
5. Click **Save**.

Each field will be saved **only for the selected sales channel**.

## 🧑‍💻 Authors

Developed and maintained by [Solution25](https://github.com/solution25com)

## 📄 License

MIT License