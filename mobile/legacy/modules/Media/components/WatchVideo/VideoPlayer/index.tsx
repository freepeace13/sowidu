import { Video, ResizeMode, AVPlaybackSource } from "expo-av"
import { ImageSource } from "expo-image"
import * as ScreenOrientation from "expo-screen-orientation"
import * as StatusBar from "expo-status-bar"
import { useMemo, useState, useRef } from "react"
import { Animated, Dimensions } from "react-native"

import Style from "./style"

// https://github.com/expo/playlist-example/blob/master/App.js
// https://github.com/mdebelj1/RN-video/blob/master/src/views/VideoPlayer.android.tsx

type VideoPlaybackStatus = {
  isLoaded: boolean
  durationMillis: number
  positionMillis: number
  playableDurationMillis: number
  shouldPlay: boolean
  isPlaying: boolean
  isBuffering: boolean
  rate: number
  shouldCorrectPitch: boolean
  volume: number
  isMuted: boolean
  audioPan: number
  isLooping: boolean
  didJustFinish: boolean
}

const initialStatus: VideoPlaybackStatus = {
  isLoaded: false,
  durationMillis: 0,
  positionMillis: 0,
  playableDurationMillis: 0,
  shouldPlay: false,
  isPlaying: false,
  isBuffering: false,
  rate: 1,
  shouldCorrectPitch: false,
  volume: 1,
  isMuted: true,
  audioPan: 0,
  isLooping: false,
  didJustFinish: false,
}

const isProcessing = (status: VideoPlaybackStatus) =>
  (status.isBuffering && status.positionMillis >= status.playableDurationMillis) || !status.isLoaded

const isNotPlayed = (status: VideoPlaybackStatus) =>
  status.isLoaded && (!status.shouldPlay || !status.isPlaying)

const extractPlaybackStatus = (status) =>
  Object.keys(initialStatus).reduce((prev, curr) => {
    if (typeof status[curr] !== "undefined") {
      prev[curr] = status[curr]
    }
    return prev
  }, {})

const WindowSize = Dimensions.get("window")

type VideoPlayerProps = {
  source: AVPlaybackSource
  autoplay?: boolean
  skipMs?: number
  cover?: ImageSource
  onError?: (error: string) => void
  onFullscreenChange?: (isFullscreen: boolean) => void
}

export default function VideoPlayer({
  source,
  cover,
  autoplay = true,
  onError,
  skipMs = 10000,
  onFullscreenChange,
}: VideoPlayerProps) {
  const playerRef = useRef<Video>(null)

  const [isFullscreen, setIsFullscreen] = useState<boolean>(false)
  const [playbackStatus, setPlaybackStatus] = useState<VideoPlaybackStatus>(initialStatus)

  const opacity = useMemo(() => new Animated.Value(0), [])

  const _handleLoad = () => {
    Animated.timing(opacity, {
      toValue: 1,
      useNativeDriver: true,
    }).start(() => {
      setPlaybackStatus((prev) => ({ ...prev, isLoaded: true }))
    })
  }

  const _handleLoadStart = () => {
    setPlaybackStatus((prev) => ({
      ...prev,
      ...initialStatus,
    }))
  }

  const _handleError = (error) => {
    onError && onError(error)
  }

  const _handlePlaybackStatusUpdate = async (status) => {
    // @ts-ignore
    if (status.error || !status.isLoaded) return

    if (status.didJustFinish) {
      isFullscreen && (await _handleFullscreenDismiss())
      await playerRef.current.stopAsync()
    }

    setPlaybackStatus((prev) => ({
      ...prev,
      ...extractPlaybackStatus(status),
    }))
  }

  const _handleFullscreenChange = (fullscreen) => {
    StatusBar.setStatusBarHidden(fullscreen, "slide")
    onFullscreenChange && onFullscreenChange(fullscreen)

    requestAnimationFrame(() => setIsFullscreen(fullscreen))
  }

  const _handleFullscreenPresent = async () => {
    await ScreenOrientation.lockAsync(ScreenOrientation.OrientationLock.LANDSCAPE_RIGHT)
    setIsFullscreen(true)
  }

  const _handleFullscreenDismiss = async () => {
    await ScreenOrientation.lockAsync(ScreenOrientation.OrientationLock.PORTRAIT_UP)
    setIsFullscreen(false)
  }

  const playVideo = () => {
    if (!playbackStatus.isLoaded) return
    playerRef.current.playAsync()
  }

  const pauseVideo = async () => {
    if (!playbackStatus.isLoaded) return
    await playerRef.current.pauseAsync()
  }

  const seekVideo = async (position: number) => {
    if (!playbackStatus.isLoaded) return
    await playerRef.current.setPositionAsync(position)
  }

  const seekForwardVideo = () => {
    const { durationMillis, positionMillis } = playbackStatus
    return seekVideo(Math.min(durationMillis, positionMillis + skipMs))
  }

  const seekBackwardVideo = () => {
    const { positionMillis } = playbackStatus
    seekVideo(Math.max(0, positionMillis - skipMs))
  }

  return (
    <Video
      usePoster
      isLooping={false}
      useNativeControls
      resizeMode={ResizeMode.CONTAIN}
      posterSource={cover}
      source={source}
      onLoad={_handleLoad}
      onLoadStart={_handleLoadStart}
      onError={_handleError}
      onPlaybackStatusUpdate={_handlePlaybackStatusUpdate}
      status={{
        shouldPlay: autoplay,
        rate: initialStatus.rate,
        isMuted: initialStatus.isMuted,
      }}
      videoStyle={Style.video}
      style={Style.player}
    />
  )
}
