## Local Setup

### 1. Using cmdline-tools
Go to android download page and download compressed file `cmdline-tools`. Extract it anywhere and you will get a folder named `cmdline-tools`.

#### 1.1. Download the essential packages
Navigate to `cmdline-tools/bin` folder, run the ff command, and if encounter error about java version, you must install latest java first.

```
./sdkmanager --list
```

If encounter following errors:
* Something related to java version incompatibility - install the latest java first.
* "Could not determine SDK root" - move all `cmdline-tools` contents into `cmdline-tools/latest` folder

#### 1.2. Install `platform-tools` and `emulator` packages.
```
./sdkmanager platform-tools emulator
```

#### 1.3. Platform-specific packages
Install `platforms`, `system-images` and `build-tools` for specific android version. For example, API level 30 (android 11), the commands will be:

NOTE: I assume you already setup env variables path `ANDROID_SDK_ROOT` and/or `ANDROID_HOME` that pointing to the path of `cmdline-tools` folder.

```
sdkmanager "platforms;android-30"
sdkmanager "build-tools;30.0.0"
sdkmanager "system-images;android-30;google_apis;x86_64"
```

#### 1.4. Create an AVD device
To create AVD device for android 11 (change `android11` to your avd name of choice), run:

```
avdmanager create avd --name android11 --package "system-images;android-30;google_apis;x86_64"
```

#### 1.5. Run the Android emulator
Once emulator is running, install the development build of the app `npm run android` then select your emulator name.

```
emulator @pixelapi34 -verbose -log-detailed -no-snapshot -fixed-scale
```
