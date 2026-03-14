### Hexagonal Architecture

### Directory Structure

### References

* (https://fideloper.com/hexagonal-architecture)[Hexagonal Architecture]
* (https://evilmartians.com/chronicles/spa-hexagon-robust-app-architecture-for-mobile-and-web)[Hexagonal Robust App Architecture]
* (https://github.com/juanm4/hexagonal-architecture-frontend/tree/master/hexagonal-architecture/src)[hexagonal architecture folder structure]

### Layers

- core - Core files
- domain - business logic & definitions
- application - mediator of domain and framework communicate through ports and adapters using interfaces
- framework - the app ui and state


### IoC Container
Business logic implementations will be bootstrap in IoC container instance and inject theme anywhere
See: https://github.com/inversify/InversifyJS


### View objects
Objects that represents domain objects that display in the UI.

Example: https://github.com/Blindpupil/family-cookbook/blob/master/src/primary/recipe/RecipeView.ts

- You are welcome to import domain types and use them to, well, type your typescript code as you please.
- The constructor is also private: it needs to be instantiated via the static fromDomain method. The reason we're passing the domain
instance and deconstructing the properties instead of just passing the properties is that domain instances can have getters required by the View that doesn't exist in the properties.
- The properties of the View don’t need to match the properties of the domain object. In this example, we transform the updatedAt Date into the string that’s to be displayed in the UI. Whether that’s a good choice or not depends solely on the needs of the app you’re developing.
- You can enhance the View class with getters, or even extra properties, as needed. In this example, I added the mealSizeIcon getter.

### Use cases
Example: https://github.com/Blindpupil/family-cookbook/blob/master/src/primary/recipe/use-cases/GetRecipesUseCase.ts

- UseCases are constructed with repositories, not with the classes that implement them. (This is the D in SOLID: Depend upon abstractions, not concretions).
- UseCases have a single public method “execute”, but can have as many private methods as needed.
- This is the only place where domain methods are allowed to be executed. Requesting the mutation of domain objects only happens within use cases.
- UseCases always return Views (or void).

Domain Repository -> Use Cases -> Service

### Service
Example: https://github.com/Blindpupil/family-cookbook/blob/master/src/primary/recipe/use-cases/index.ts

### Resource

All communication your application has with the external world is implemented by the Resource class. And when I say all, I mean all. When a component needs some data to display, it doesn’t care whether that data comes from a REST API, a GraphQL API, local storage, the state management store, WebSockets, edge functions, firebase, supabase, cookies, IndexedDB, or anything else. The resource is the only one that needs to deal with those issues.

Example: https://github.com/Blindpupil/family-cookbook/blob/master/src/secondary/recipe/RecipeResource.ts

What’s important here is the RecipeResource implements RecipeRepository part. You'll see a TypeScript error in your IDE until all the methods in the Repository are implemented in the Resource.

In the simple example shown here, the RecipeResource only fetches information from a REST API. And so it's constructed only with the RestClient (which is just a wrapper over fetch).

If later we'd like to replace fetch with Axios or some other HTTP client, we'd be able to do so without interfering with the Repository. Also, if the Resource would also require to connect with a GraphQL API, for example, you can simply add your own graphQLClient to the constructor and use it.

### API Adapters

The response that the API provides will hardly aver match exactly with your domain in the frontend. An adapter is what will allow you to transform the response to domain objects.

Example: https://github.com/Blindpupil/family-cookbook/blob/master/src/secondary/recipe/ApiRecipe.ts


### Data flow diagram
https://miro.medium.com/v2/resize:fit:1100/format:webp/1*sk2RpRmi42ZLSLw-G-tPpg.png

Domain
  - Auth
    - Model
    - Repository
  - Account
    - Model
    - Repository
  - Recipe
    - Model
    - Repository
Infrastructure
  - Recipe
    - Resource - Sending request to external sources, implements domain repository
    - ApiAdapter - Represents data coming from API client response, allow transforms into domain objects
  - Auth
    - Resource - Sending request to external sources, implements domain repository
    - ApiAdapter - Represents data coming from API client response, allow transforms into domain objects
  - Account
    - Resource - Sending request to external sources, implements domain repository
    - ApiAdapter - Represents data coming from API client response, allow transforms into domain objects
Application
  - Recipe
    - UseCase - Execute domain repository methods, transforms domain objects into View objects
    - Service - Combination of use cases in a class
    - View - Represents domain objects that use directly in the UI
Framework
  - Components
  - Store
  - App.tsx
