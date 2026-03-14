import { Utils as CoreUtils } from "core-module"
import { Camera, CameraCapturedPicture, CameraType, FlashMode, PermissionStatus } from "expo-camera"
import React, { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Pressable, StyleSheet, View } from "react-native"
import { AnimatedCircularProgress } from "react-native-circular-progress"
import PagerView from "react-native-pager-view"
import { Appbar, Icon, IconButton, Modal, Portal, Text, useTheme } from "react-native-paper"

import * as MediaConstants from "../../../../constants"
import { Utils as MediaUtils } from "../../../../services"

const VIDEO_MAX_DURATION = MediaConstants.VideoMaxDurationSeconds
const PHOTO_QUALITY = MediaConstants.ImageQuality

const FlashModes = Object.keys(FlashMode).filter((item) => {
  return isNaN(Number(item))
})

const elapsedSeconds = (start: Date) => (new Date().getTime() - start.getTime()) / 1000

const getFlashIcon = (mode: FlashMode) =>
  ({
    [FlashMode.auto]: "flash-auto",
    [FlashMode.on]: "flash",
    [FlashMode.off]: "flash-off",
    [FlashMode.torch]: "flash-alert",
  }[mode])

type PagerProp = {
  page: number
  go: (page: number) => void
  move: (delta: number) => void
}

type PageProps = {
  pager: PagerProp
  onError?: (message: string) => void
  // flashMode: FlashMode
  // onFlashModeChanged: () => void
  // cameraType: CameraType
  // onCameraTypeChanged: () => void
}

const DEFAULT_FLASH_MODE = FlashMode.auto
const DEFAULT_CAMERA_TYPE = CameraType.back

function useCameraSettings() {
  const [cameraType, setCameraType] = useState<CameraType>(DEFAULT_CAMERA_TYPE)
  const [flashMode, setFlashMode] = useState<FlashMode>(DEFAULT_FLASH_MODE)

  const switchCameraType = () => {
    setCameraType((prevCameraType) =>
      prevCameraType === CameraType.back ? CameraType.front : CameraType.back,
    )
  }

  const switchFlashMode = () => {
    setFlashMode((prevFlashMode) => {
      const index = FlashModes.indexOf(prevFlashMode)
      const next = (index + 1) % FlashModes.length
      return FlashModes[next] as FlashMode
    })
  }

  return {
    cameraType,
    switchCameraType,
    flashMode,
    switchFlashMode,
  }
}

type VideoSource = {
  uri: string
}

type VideoRecordPageProps = PageProps & {
  onRecordCompleted: (source: VideoSource) => void
  onClose: () => void
}

function VideoRecordPage({ onClose, onError, onRecordCompleted }: VideoRecordPageProps) {
  const cameraRef = useRef<Camera>()
  const [isRecording, setIsRecording] = useState(false)
  const [secondsElapsed, setSecondsElapsed] = useState(0)
  const [isReady, setIsReady] = useState(false)
  const { flashMode, switchFlashMode, cameraType, switchCameraType } = useCameraSettings()
  const { colors } = useTheme()

  useEffect(() => {
    let timerId

    function prepare() {
      timerId = setInterval(() => {
        setSecondsElapsed((time) => {
          console.log(time)
          return (time += 1)
        })
        !isRecording && clearInterval(timerId)
      }, 1000)
      !isRecording && clearInterval(timerId)
    }
    isRecording && prepare()

    return () => {
      clearInterval(timerId)
    }
  }, [isRecording])

  const startVideoRecord = async () => {
    if (cameraRef.current) {
      try {
        const videoRecordPromise = cameraRef.current.recordAsync({
          maxDuration: VIDEO_MAX_DURATION, // 10 minutes
          mute: false,
        })
        if (videoRecordPromise) {
          setSecondsElapsed(0)
          setIsRecording(true)
          const source = await videoRecordPromise
          onRecordCompleted(source)
        }
      } catch (error) {
        console.warn(error)
      }
    }
  }

  const stopVideoRecord = () => {
    if (cameraRef.current) {
      cameraRef.current.stopRecording()
      setIsRecording(false)
    }
  }

  const onCameraReady = () => {
    setIsReady(true)
  }

  return (
    <View style={styles.page}>
      <Appbar.Header dark mode="center-aligned" style={{ backgroundColor: "transparent" }}>
        <Appbar.BackAction onPress={onClose} />
        <Appbar.Content
          title={
            <View
              style={{
                flex: 1,
                alignItems: "center",
                justifyContent: "center",
                flexDirection: "row",
                columnGap: 6,
              }}
            >
              <Icon source="record" color={colors.error} size={18} />
              <Text variant="titleLarge" style={{ color: colors.surface }}>
                {MediaUtils.convertMsToHM(secondsElapsed)}
              </Text>
            </View>
          }
        />
        <Appbar.Action icon={getFlashIcon(flashMode)} onPress={switchFlashMode} />
      </Appbar.Header>
      <View style={{ flex: 1 }}>
        <Camera
          ref={cameraRef}
          type={cameraType}
          flashMode={flashMode}
          ratio="16:9"
          onCameraReady={onCameraReady}
          onMountError={(e) => onError && onError(e.message)}
          style={StyleSheet.absoluteFillObject}
        />
        <Pressable onPress={CoreUtils.doubleTap(switchCameraType)} style={{ flex: 1 }} />
      </View>
      <View style={styles.control}>
        <IconButton icon="folder-open" onPress={() => {}} size={28} iconColor={colors.surface} />
        <AnimatedCircularProgress
          width={3}
          size={70}
          tintColor={colors.error}
          backgroundColor={colors.surface}
          fill={(secondsElapsed / VIDEO_MAX_DURATION) * 100}
        >
          {(_) => (
            <IconButton
              animated
              disabled={!isReady}
              theme={{ colors: { onSurfaceDisabled: colors.error } }}
              onPress={!isRecording ? startVideoRecord : stopVideoRecord}
              iconColor={colors.error}
              size={80}
              icon={isRecording ? "stop" : "record"}
            />
          )}
        </AnimatedCircularProgress>
        <IconButton
          icon="camera-flip"
          onPress={switchCameraType}
          size={28}
          iconColor={colors.surface}
        />
      </View>
    </View>
  )
}

type ImageCapturePageProps = PageProps & {
  onImageCaptured: (source: CameraCapturedPicture) => void
  onClose: () => void
}

function ImageCapturePage({ onClose, onImageCaptured, onError }: ImageCapturePageProps) {
  const cameraRef = useRef<Camera>()
  const [isReady, setIsReady] = useState(false)
  const { flashMode, switchFlashMode, cameraType, switchCameraType } = useCameraSettings()
  const { colors } = useTheme()

  const takePicture = async () => {
    if (cameraRef.current) {
      try {
        const source = await cameraRef.current.takePictureAsync({
          quality: PHOTO_QUALITY,
          base64: false,
          skipProcessing: true,
        })
        onImageCaptured(source)
      } catch (error) {
        console.warn(error)
      }
    }
  }

  const onCameraReady = () => {
    setIsReady(true)
  }

  return (
    <View style={styles.page}>
      <Appbar.Header dark mode="center-aligned" style={{ backgroundColor: "transparent" }}>
        <Appbar.BackAction onPress={onClose} />
        <Appbar.Content title="" />
        <Appbar.Action icon={getFlashIcon(flashMode)} onPress={switchFlashMode} />
      </Appbar.Header>
      <View style={{ flex: 1 }}>
        <Camera
          ref={cameraRef}
          type={cameraType}
          flashMode={flashMode}
          ratio="16:9"
          onCameraReady={onCameraReady}
          onMountError={(e) => onError && onError(e.message)}
          style={StyleSheet.absoluteFillObject}
        />
        <Pressable onPress={CoreUtils.doubleTap(switchCameraType)} style={{ flex: 1 }} />
      </View>
      <View style={styles.control}>
        <IconButton icon="folder-open" onPress={() => {}} size={28} iconColor={colors.surface} />
        <AnimatedCircularProgress
          width={3}
          size={70}
          tintColor={colors.error}
          backgroundColor={colors.surface}
          fill={0}
        >
          {(_) => (
            <IconButton
              animated
              disabled={!isReady}
              theme={{ colors: { onSurfaceDisabled: colors.surface } }}
              onPress={takePicture}
              iconColor={colors.surface}
              size={80}
              icon="record"
            />
          )}
        </AnimatedCircularProgress>
        <IconButton
          icon="camera-flip"
          onPress={switchCameraType}
          size={28}
          iconColor={colors.surface}
        />
      </View>
    </View>
  )
}

function CameraPager({
  initialPage = 0,
  pages,
  scrollEnabled = true,
  detachInactive = false,
  orientation = "horizontal",
}) {
  const viewPager = useRef<PagerView>()
  const [page, setPage] = useState(0)

  const onPageSelected = (e) => {
    setPage(e.nativeEvent.position)
  }

  const move = (delta: number) => {
    const newPage = page + delta
    go(newPage)
  }

  const go = (page: number) => {
    viewPager?.current?.setPage(page)
  }

  const renderContent = useCallback(
    (content, position) => {
      if (detachInactive && page !== position) {
        return null
      }
      return content({ pager: { page, move, go } as PagerProp })
    },
    [page],
  )

  const scrollOrientation = useMemo(() => orientation as "horizontal" | "vertical", [orientation])

  return (
    <PagerView
      ref={viewPager}
      initialPage={initialPage}
      onPageSelected={onPageSelected}
      scrollEnabled={scrollEnabled}
      orientation={scrollOrientation}
      style={{ flex: 1 }}
    >
      {pages.map((content, key) => (
        <View key={key} style={{ flex: 1 }} collapsable={false}>
          {renderContent(content, key)}
        </View>
      ))}
    </PagerView>
  )
}

enum MediaType {
  video = "video",
  image = "image",
}

type CameraResponse = {
  type: "video" | "image"
  source: CameraCapturedPicture | VideoSource
}

type CameraPageProps = {
  onSuccess: (response: CameraResponse) => void
  onError?: (message: any) => void
  onClose: () => void
}

function CameraPage({ onSuccess, onError, onClose }: CameraPageProps) {
  const onImageCaptured = (source: CameraCapturedPicture) => {
    onSuccess({ type: "image", source })
  }

  const onVideoRecorded = (source: VideoSource) => {
    onSuccess({ type: "video", source })
  }

  return (
    <CameraPager
      detachInactive
      pages={[
        (props) => (
          <ImageCapturePage
            {...props}
            onClose={onClose}
            onImageCaptured={onImageCaptured}
            onError={onError}
          />
        ),
        (props) => (
          <VideoRecordPage
            {...props}
            onClose={onClose}
            onRecordCompleted={onVideoRecorded}
            onError={onError}
          />
        ),
      ]}
    />
  )
}

type CameraModalProps = CameraPageProps & {
  onClose: () => void
}

export default function CameraModal({ onSuccess, onError, onClose }: CameraModalProps) {
  const [hasPermission, setHasPermission] = useState(null)
  const { colors } = useTheme()

  useEffect(() => {
    ;(async () => {
      const { status } = await Camera.requestCameraPermissionsAsync()
      setHasPermission(status === PermissionStatus.GRANTED)
    })()
  }, [])

  if (hasPermission === null) {
    return <View />
  }

  if (hasPermission === false) {
    return <Text variant="titleLarge">No access to camera</Text>
  }

  return (
    <Portal>
      <Modal
        visible
        onDismiss={onClose}
        contentContainerStyle={{ flex: 1, backgroundColor: colors.onBackground }}
      >
        <CameraPage onSuccess={onSuccess} onError={onError} onClose={onClose} />
      </Modal>
    </Portal>
  )
}

const styles = StyleSheet.create({
  page: {
    ...StyleSheet.absoluteFillObject,
    justifyContent: "space-between",
  },
  control: {
    paddingVertical: 18,
    flexDirection: "row",
    width: "100%",
    alignItems: "center",
    justifyContent: "space-around",
  },
})
