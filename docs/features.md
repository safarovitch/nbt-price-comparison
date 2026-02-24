# Features Overview

This page breaks down the primary sub-systems governing the NBT Price Comparison platform.

## 1. Localization & RTL

Full multilingual support (`en`, `ru`, `tg`) built precisely into the routing, eloquent models, and UI. Handled deeply via the custom `LocaleHelper` and Spatie Translatable. Locale routing is mandated via middleware for most client-facing routes.

## 2. Financial Products Catalog

A catalog logic engineered to parse, check, and compare different typed financial products (`credits`, `insurance`, `deposits`). Administrators can categorize them securely via the backend.

## 3. Dynamic Calculators

Integrated Loan, Credit, and Deposit calculators implemented entirely in Vue reactive states combined with API calculations when necessary. Highly scalable format.

## 4. Exchange Rates Manager

A dedicated currency exchange module that retrieves and computes exchange data visually, offering immediate conversions mimicking real-life exchange boards.

## 5. ATMs & Branches Locator

Leverages `DeviceLocation` models heavily tied to robust maps using **Leaflet.js** to pinpoint bank branches or kiosks globally (or regionally). Employs clustering for heavy data points.

## 6. Ratings & Application Submission (Reviews)

System for clients to analyze bank performances and submit applications / reviews natively into the platform for moderation.

## 7. Role-Based Admin Panel

A highly secured backend view managed completely within Inertia wrapped views. It utilizes **Spatie Permissions** to filter access, manage incoming organizations, dictate products out in the wild, and oversee applications.
