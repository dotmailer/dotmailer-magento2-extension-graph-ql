# Dotdigital EmailGraphQl
[![Packagist Version](https://img.shields.io/packagist/v/dotdigital/dotdigital-magento2-extension-graph-ql?color=green&label=stable)](https://github.com/dotmailer/dotmailer-magento2-extension-graph-ql/releases)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE.md)

## About this module
**Dotdigitalgroup_EmailGraphQl** supports our [core module](https://github.com/dotmailer/dotmailer-magento2-extension).
It provides type and resolver information for Magento to generate endpoints for:
- fetching tracking configuration data from the instance
- fetching email capture configuration from the instance
- retrieving order data for customers and guests on the order confirmation page 
- updating the quote email address

## Requirements
- This module requires the `Dotdigitalgroup_Email` module v4.14.0+

## Installation
- This module is included in our core extension. Please refer to [these instructions](https://github.com/dotmailer/dotmailer-magento2-extension#installation) to install via the Magento Marketplace.

## Endpoints

**Queries**
```
query getTrackingData {
        trackingData {
            page_tracking_enabled
            roi_tracking_enabled
            wbt_profile_id
            region_prefix
        }
    }

query isEasyEmailCaptureNewsletterEnabled {
        emailCaptureNewsletter {
            is_enabled
        }
    }

query isEasyEmailCaptureCheckoutEnabled {
        emailCaptureCheckout {
            is_enabled
        }
    }

query getOrderDetails($orderNumber: String!) {
        orderData(order_id: $orderNumber) {
            items
            total
        }
    }
    
query getProductBrandValue($product_ids: [String]!) {
       productBrands(product_ids: $product_ids) {
           items {
               brand
               product_id
           }
       }
    }
```

**Mutations**
```
mutation updateQuoteEmail($email: String!, $cartId: String!) {
        updateQuoteEmail(
            email: $email,
            cartId: $cartId
        )
    }
```

## Changelog

### 1.2.0

##### What's new
- We've added a new endpoint to retrieve the nominated brand attribute.

##### Bug fixes
- We updated the module's PSR-4 filepath.

### 1.1.0

##### What's new
- This module has been renamed `dotdigital/dotdigital-magento2-extension-graph-ql`.

##### Improvements
- `setup_version` has been removed from module.xml.

### 1.0.0
- Initial release
