# Savvy AI for Laravel
Domain knowledge artificial intelligence framework for Laravel

---
## [0.6.3] - 2023-05-26

### Updated
- Updated the way media links are generated to be more relevant
- Updated media links in rich replies to be at most 3

---
## [0.6.2] - 2023-05-25

### Added
- Added support for generating replies that include media (links) to be rendered along with the message

---
## [0.6.1] - 2023-05-23

### Updated
- Updated classification prompt to reduce the chances of hallucinating delegates
- Updated reply generation with reply validation for unknown context and off-topic inspection

---
## [0.6.0] - 2023-05-01

### Added
- Added snippet resolver injection with a more specific name to avoid collisions
- Added support for summarizing for training or splitting with a delimiter

### Updated
- Updated AIServiceContract signature to only require text
- Updated trainable contract and impl to own the splitter factory method
- Disable reply validation to open up the model for move conversational replies
- Removed unused classes for sanitizing, segmenting, summarizing, and tokenizing
- Removed all unused model attributes for Chatbots, and Agents
- Simplified text splitting and vectorizing during training

### Fixed
- Fixed issue where max tokens from the Dialogue model were not used when generating replies
- Fixed issue where splitter would reach an infinite loop due to sentence length overflow

---
## [0.5.0] - 2023-04-14

### Added
- Added support for relating dialogue and messages
- Added fallback replies for unknown context and off-topic replies
- Added initial tests for replies and messages

### Fixed
- Fixed issues with regular expressions to parse Prompt Snippets

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
