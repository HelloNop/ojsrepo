API & Mobile App Development Plan
Goal
Enable the OJS Repository to serve data to mobile clients via a RESTful API and build a Flutter mobile application for end-users.

User Review Required
IMPORTANT

The API installation will install Laravel Sanctum and modify routes/api.php.

Proposed Changes
Phase 1: API Development (Laravel)
[NEW]

routes/api.php
Register public endpoints:
GET /journals
GET /articles
GET /articles/{article}
[NEW]

ApiController.php
Implement logic similar to

PublicController
but returning JSON.
Methods: journals(),

articles()
, showArticle().
[NEW]

ArticleResource.php
Transform

Article
model to JSON.
Include related

Journal
and

Author
data.
Phase 2: Mobile App (Flutter)
Project Structure
lib/main.dart: App entry point.
lib/services/api_service.dart: HTTP client logic.
lib/models/: Dart models (Article, Journal).
lib/screens/:
home_screen.dart
search_screen.dart
article_detail_screen.dart
Verification Plan
API Testing
Automated: Create tests/Feature/ApiTest.php.
Manual: Use curl or Postman to verify JSON response structure.
curl http://localhost/api/articles
Mobile App Testing
Run on Android Emulator / iOS Simulator.
Verify data loading and navigation.
