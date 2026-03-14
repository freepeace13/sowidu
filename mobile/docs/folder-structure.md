# Domain Layer
+ Domain
  #  Core Domain
  + Core
    + Contracts
      - IPersistedStorage.ts
      - IHttp.ts

  #  Media Domain
  + Media
    + Http
      + Controllers
        - MediaListController.ts
      + Resource
        - IMedia.ts
    + Services
      - IMediaListService.ts
      - IMediaUploadService.ts

  #  Teams Domain
  + Teams
    + Http
      # injects domain-specific services
      + Controllers
        - CurrentUserController.ts
      + Resource
        - IMedia.ts
    # injects implementation of @domain/core/IHttp
    + Services
      - IMediaListService.ts
      - IMediaUploadService.ts

#  Broker of Domain & Application
+ Modules
  #  Common Module
  + Common
    + Api
      + Errors
        - ApiError.ts
      + Adapters
        - AxiosApiAdapter.ts
      + Support
      - ApiClient.ts
      - ApiAdapterFactory.ts
      - index.ts
    + Normalizers
      + Adapters
        - 
      - INormalizer.ts
    + Config
      - index.ts

  #  Media Module
  + Media
    + Components
        + MediaList
            + Components
              + ListFiles
                  + Services
                  + Hooks
                  - ListFiles.tsx
                  - ListFilesStyle.ts
                  - index.ts
            + Services
            - MediaList.tsx
            - MediaListStyle.ts
            - index.ts
        + MediaShowcase
            + Components
                + MediaPlayer
                    + Services
                    + Hooks
                    + Store
                    - MediaPlayer.tsx
                    - MediaPlayerStyle.ts
                    - index.ts
            - MediaShowcase.tsx
            - MediaShowcaseStyle.ts
            - index.ts
    + Services
          /** instance of a class that implements @modules/domain/media/services/IMediaListService */
        - MediaUploadService.ts

          /** instance of a class that implements @modules/domain/media/services/IMediaListService */
        - MediaListService.ts 
    + Hooks
    + State
      - initialState.ts
      + Models
          - Media.ts
          - Permission.ts
    - index.ts

# View Layer
+ Application
  + Navigation
  + Screens
  + Theme
  + Store
  - App.tsx
