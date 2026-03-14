### Modules/Contexts
  - Account Management
    - Login/Register
    - Password Updates
    - Profile Info/Updates
  - Media Library
    - File list/showcase/upload
    - File Share user/showcase

- src
  - core
  - domain
    - account-management
    - media-library
  - infrastructure
    - account-management
    - media-library
  - application
    - account-management
    - media-library
  - framework
    - components
    - features
    - navigation
    - store
    - App.tsx
 
### Infrastructure

- account-management
  - Resource - Sending request to external sources, implements domain repository
  - ApiAdapter - Represents data coming from API client response, allow transforms into domain 

### Application

- account-management
  - UseCase - Execute domain repository methods, transforms domain objects into View objects
  - Service - Combination of use cases in a class
  - View - Represents domain objects that use directly in the UI

### Framework

- features
  - account-management
    - Register
      - components
      - constants
      - hooks
      - store
        - actions.ts
        - slice.ts
      - Register.tsx
      - RegisterStyle.ts
      - index.ts
    - Login
      - components
      - constants.ts
      - Login.tsx
      - LoginStyle.ts
      - index.ts

