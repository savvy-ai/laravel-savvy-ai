# Savvy AI for Laravel
Domain knowledge artificial intelligence framework for Laravel

---
## [0.4.0] - 2023-04-08

### Added
- Added support for Dialogue Prompt Snippets by setting `snippets.namespace` config
- Added the expand command to test snippet expansion via the console

### Updated
- Updated the way the snippets are resolved to allow for more flexibility

### Removed
- Removed dependency on `Filament`
- Removed dependency on `Twilio`
- Removed dependency on `Pusher`

---
## [0.3.0] - 2023-04-05

### Added
- Added a trainable contract to enforce the fetching of a knowledge/statement repository
- Added a trainable parameter to main train() method

### Updated
- Updated the way the vector store service handle persistence

---
## [0.2.3] - 2023-04-04

### Updated
- Update all models to extend base model and to use the HasFactory and HasUuids trait

---
## [0.2.2] - 2023-04-04

### Fixed
- Fixed issue where seeding would fail due to public visibility of model newFactory()

---
## [0.2.1] - 2023-04-04

### Added
- Added a new base model with new factory method to be extended by all models

### Updated
- Normalized use of UUID in migrations

---
## [0.2.0] - 2023-04-04

### Added
- Added a whole new framework for conducting training and chat
- Added the concept of trainable entities
- Added several contracts to allow weaker typing
- Added a set of traits that makes it easy to fulfill contracts
- Added new commands for testing the new framework for setting chat and training
- Added migrations for trainables, chats, messages, chatbots, agents, and dialogues

*Do not use this version or any version prior to 1.0 on production.

---
## [0.1.1] - 2023-03-22

### Added
- Added model properties such as fillables and casts to improve integration with filament and fix mass assignment issues

---
## [0.1.0] - 2023-03-22

### Added
- Added boilerplate package with functional prototype for AI-powered chat
