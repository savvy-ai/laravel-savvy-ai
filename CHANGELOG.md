# Savvy AI for Laravel
Domain knowledge artificial intelligence framework for Laravel

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
