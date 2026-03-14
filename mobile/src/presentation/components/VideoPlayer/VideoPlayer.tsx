import { Video, ResizeMode, AVPlaybackSource, AVPlaybackStatus } from "expo-av"
import { ImageSource } from "expo-image"
import * as ScreenOrientation from "expo-screen-orientation"
import * as StatusBar from "expo-status-bar"
import { useMemo, useState, useRef } from "react"
import { Animated, Image, View } from "react-native"

import Style from "./VideoPlayerStyle"

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

const extractPlaybackStatus = (status: AVPlaybackStatus) =>
  Object.keys(initialStatus).reduce((prev, curr) => {
    if (typeof status[curr] !== "undefined") {
      prev[curr] = status[curr]
    }
    return prev
  }, {} as VideoPlaybackStatus)

interface Props {
  source: AVPlaybackSource
  autoplay?: boolean
  skipMs?: number
  cover?: ImageSource
  onError?: (error: string) => void
  onFullscreenChange?: (isFullscreen: boolean) => void
}

function VideoPlayer(props: Props) {
  const { source, cover, autoplay = false, onError, skipMs = 10000, onFullscreenChange } = props
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

  const _handleError = (error: any) => {
    onError && onError(error)
  }

  const _handlePlaybackStatusUpdate = async (status: AVPlaybackStatus) => {
    // @ts-ignore
    if (!status.error && status.isLoaded) {
      if (status.didJustFinish) {
        if (isFullscreen) {
          await _handleFullscreenDismiss()
        }

        if (playerRef.current) {
          await playerRef.current.stopAsync()
        }
      }

      setPlaybackStatus((prev) => ({
        ...prev,
        ...extractPlaybackStatus(status),
      }))
    }
  }

  const _handleFullscreenDismiss = async () => {
    await ScreenOrientation.lockAsync(ScreenOrientation.OrientationLock.PORTRAIT_UP)
    setIsFullscreen(false)
  }

  return (
    <Video
      ref={playerRef}
      usePoster
      isLooping={false}
      useNativeControls
      resizeMode={ResizeMode.CONTAIN}
      PosterComponent={({ source, style }) => (
        <View>
          <Image source={source} style={style} />
        </View>
      )}
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

export default VideoPlayer
