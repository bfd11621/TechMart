# iRACK IT Solution — API Documentation Summary

The master REST API specification is located in the root repository at [`API_DOCUMENTATION.md`](../API_DOCUMENTATION.md).

## Quick Reference to Modules:
1. **Health Check & Diagnostics**: `GET /api/v1/health`
2. **Public Product Catalogue**: `GET /api/v1/public/products`, `GET /api/v1/public/categories`, `GET /api/v1/public/brands`
3. **Datasheet Downloads**: `GET /api/v1/public/products/{id}/datasheet`
4. **Public RFQ Submission**: `POST /api/v1/public/rfqs` (Rate limited: 10/min)
5. **Public Contact & Hub Inquiries**: `POST /api/v1/public/contact` (Rate limited: 10/min)
6. **Authentication & Password Recovery**: `POST /api/v1/auth/login`, `POST /api/v1/auth/forgot-password` (Rate limited: 5/min)
7. **Staff Profile**: `GET /api/v1/auth/me`, `PUT /api/v1/auth/profile`, `PUT /api/v1/auth/change-password`
8. **Staff User Management**: `/api/v1/admin/users` (CRUD, status toggles, password resets)
9. **Role-Based Access Control**: `/api/v1/admin/roles`, `/api/v1/admin/permissions`
10. **Store Branch Management**: `/api/v1/admin/stores`
11. **Product Catalogue & Specs Management**: `/api/v1/admin/products`
12. **Multi-Store Inventory & Movements**: `/api/v1/admin/inventory`
13. **Quotation Request (RFQ) Processing**: `/api/v1/admin/rfqs`
14. **Quotation Preparation & PDF Generation**: `/api/v1/admin/quotations`
15. **Sales Order Conversion & Payments**: `/api/v1/admin/sales-orders`
16. **Business Intelligence & ECharts Analytics**: `/api/v1/admin/analytics`
17. **Branded PDF & CSV Reporting**: `/api/v1/admin/reports`
18. **Website Content CMS & System Configuration**: `/api/v1/admin/settings`, `/api/v1/admin/banners`, `/api/v1/admin/content/{slug}`

Please refer to [`API_DOCUMENTATION.md`](../API_DOCUMENTATION.md) for full JSON payload schemas, response structures, and HTTP status codes.
