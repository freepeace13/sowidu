# Dev Notes

## Workflow

-   Create your `new_branch` from main
    > `git checkout -b your_new_branch main && git push -u origin your_new_branch`
-   When your'e done, you will create a [PR](https://github.com/sowiduteam/app-web/pulls) to branch `main`.
-   Assign reviewer (if any), after it is approved and merged to `main`
-   You can now **merge** it to `staging` branch
    > Branch **`staging`** can detect changes and will start deploying to **staging server**

## Notes

## Code / Library Clean up

List down suspected **unused** packages to be removed later.

-   `nyholm/psr7`
-   `opis/closure`
-   `spatie/temporary-directory` - Laravel has `deleteFileAfterSend` ie:

```
Response::download($path, $fileName, ['Content-Type: application/zip'])->deleteFileAfterSend(true);
```

-   `symfony/psr-http-message-bridge`
