# Mobile Client

Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br/><br/>

## Design

- [System Design](https://xd.adobe.com/view/67ef7dc9-ff47-4079-81e1-8fd5caafde1d-32bc/grid)
- [Mockups](https://xd.adobe.com/view/203dcd68-86d3-456c-9af1-45a4902689d1-756e/grid)

## Requirements

- VSCode
- Node (v18.18.0)
- Device Simulator
  _ Android Studio (Android Development)
  _ XCode (iOS Development)
  <br/><br/>

# Setup

We support 2 platforms (Android, iOS)<br/><br/>

### Ubuntu (Android)

Require:  https://www.oracle.com/in/java/technologies/downloads/#jdk17-linux 
Follow: https://docs.expo.dev/workflow/android-studio-emulator/#set-up-android-studios-tools

```
$ apt install openjdk-17-jdk openjdk-17-jre
```

Next, set your Java  environment path in the /etc/environment file:

```
$ nano /etc/environment
```

Add the following line:

```
JAVA_HOME="/usr/lib/jvm/jdk-17"
```

Next, activate the Java environment variable with the following command:

```
$ source /etc/environment
```


### Mac (IOS)

Follow: https://docs.expo.dev/workflow/ios-simulator/
<br/><br/>

# Development

Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.<br/><br/>

### VSCode <b>(Recommended)</b>

We use linter to format code and automatically when opening the project folder since `.vscode` workspace settings is included in the repository. <b>DO NOT COMMIT ANY CHANGES IN THIS FOLDER!!</b><br/><br/>

### Components Development (Storybook)

UI base components are built and render in a storybook. Assuming you already setup your local machine to run android/ios simulator. In the root of the project run:

```
npm run start:storybook
```

<br/>

### Local Development

You can either use real device or simulator. Either way, run in the project root:

```
npm run start
```

### Transfer file from local to emulator with ADB
```
// Will push file to internal storage
$ adb push <local file path> ./storage/emulated/0/Download
```

### Adding native modules

After installing native modules using `npx expo install [package-name]` you need to rebuild the app. To rebuild the development app and run on device/emulator `npm run android` and choose which device the new built app to be install.

To rebuild the `staging` internal distribution run `npm run build:staging` or `npm run build` to rebuild the `production` internal distribution and safely merge your changes.

Git branch channels:
- `1.x-dev` = `staging`
- `main` = `prouction`

### References

* [https://www.patterns.dev/react](React Pattern)
* [https://github.com/juanm4/hexagonal-architecture-frontend/tree/master/hexagonal-architecture/src](Hexagonal Architecture)
