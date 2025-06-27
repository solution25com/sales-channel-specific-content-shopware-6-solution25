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


# MultiChannel Content Plugin - API Documentation
 
This documentation describes the custom Admin API endpoints provided by the **MultiChannel Content Plugin** for Shopware 6.  
This plugin allows administrators and backend services to manage product-specific content for individual sales channels.  
It supports **fetching**, **saving**, and **deleting** customized content and related images per sales channel.
 
---
 
## Get Sales Channel Specific Content
 
**Endpoint**  
`GET /api/product/sales-channel-content/{id}`
 
**Description**  
Fetches all sales channel-specific content and associated images for a given product.
 
**Request Headers**
```
Authorization: Bearer <your-access-token>  
Content-Type: application/json
```
 
**Example Request**
```
GET /api/product/sales-channel-content/5b6a139e54e54ed7b7997c71f6f56f9e
```
 
**Successful Response**
```json
{
  "status": "success",
  "message": "Sales Channel Specific Content and Images fetched successfully",
  "data": [ /* List of content objects with images */ ]
}
```
 
---
 
## Save/Update Sales Channel Specific Content
 
**Endpoint**  
`POST /api/product/sales-channel-content/{id}`
 
**Description**  
Saves or updates sales channel-specific content and images for the given product.
 
**Request Headers**
```
Authorization: Bearer <your-access-token>  
Content-Type: application/json
```
 
**Example Request Body**
```json
{
  "extensions": {
    "salesChannelSpecificContent": [
      {
        "id": "optional-content-id",
        "salesChannelId": "sales-channel-id",
        "longDescription": "Long description text",
        "metaDescription": "Meta description text",
        "metaKeywords": "keywords",
        "metaTitle": "Title",
        "shortDescription": "Short description",
        "productName": "Product name",
        "productFeatures": "Features",
        "whatsIncluded": "Included items",
        "coverImageId": "media-id",
        "wholesalePrice": 10.00,
        "retailPrice": 20.00
      }
    ],
    "salesChannelSpecificImages": [
      {
        "SalesChannelContentId": "content-id",
        "mediaId": "media-id",
        "position": 1
      }
    ]
  }
}
```
 
**Successful Response**
```json
{
  "message": "Sales Channel Specific Content and Images saved successfully"
}
```
 
**Error Response**
```json
{
  "error": "Invalid data format"
}
```
 
---
 
## Delete Sales Channel Specific Content
 
**Endpoint**  
`DELETE /api/product/sales-channel-content/{SalesChannelContentId}`
 
**Description**  
Deletes the sales channel-specific content and all related images for the provided SalesChannelContent ID.
 
**Request Headers**
```
Authorization: Bearer <your-access-token>  
Content-Type: application/json
```
 
**Example Request**
```
DELETE /api/product/sales-channel-content/73f5a9c62d4a4c56aa11cf48d323e0bc
```
 
**Successful Response**
```json
{
  "message": "Sales Channel Specific Content and Images deleted successfully"
}
```
 
**Error Response**
```json
{
  "error": "Content not found"
}
```
 
---
 
## Authentication
 
All endpoints require a valid Admin API **Bearer token**.  
You can obtain this via the standard [Shopware Admin API authentication process](https://developer.shopware.com/docs/resources/api-guide/admin-api/authentication.html).

## 🧑‍💻 Authors

Developed and maintained by [Solution25](https://github.com/solution25com)

## 📄 License

MIT License
