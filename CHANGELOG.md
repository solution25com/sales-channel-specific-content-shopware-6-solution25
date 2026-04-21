# Changelog

All notable changes to this project will be documented in this file.

## [1.1.1] - 2026-04-21  

  - Added variant-specific sales-channel content support with parent fallback.
  - Prevented empty alternative content from overwriting default product data.
  - Fixed SEO keywords mapping so values render through Shopware keywords.
  - Removed unsupported Product URL field from the admin UI.
  - Reordered admin sections to Product Details, Description, Additional Information, SEO Information.

---

## [1.1.0] -  2026-04-20  

####  Added  

-  **Compatibility with shopware 6.7.** 

---


## [1.0.5] - 2025-06-10  
##  Improvements  

-  **Removed unused controller** from registered routes to reduce unnecessary load and clean up routing.  
-  **Added missing JavaScript file** to ensure all admin-side features function properly.  
-  **Performed internal code review fixes** to align with Shopware best practices and coding standards.  
-  **Improved product detail loading logic** to better reflect sales channel-specific content on the storefront.  

---


## [1.0.4] - 2025-05-20

###  Features

- Adds a **"Content" tab** to the product detail view in the Shopware admin.
- Allows merchants to define unique product data **per sales channel**.
- Supported fields include:
  - Product name and SEO-friendly URL
  - Short and long descriptions
  - SEO title, meta keywords, and meta description
  - “What’s included” section
  - Product features
  - Wholesale and retail pricing

---


