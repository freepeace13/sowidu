import * as SplashScreen from "expo-splash-screen"
import React, {
  useState,
  useEffect,
  useCallback,
  useRef,
  ReactNode,
  FunctionComponent,
} from "react"
import { Animated, View } from "react-native"

import Style from "./AnimatedSplashScreenStyle"

interface AnimatedSplashScreenProps {
  image: any
  children: ReactNode
}

const AnimatedSplashScreen: FunctionComponent<AnimatedSplashScreenProps> = ({
  image,
  children,
}) => {
  const animation = useRef(new Animated.Value(1)).current
  const [isAppReady, setAppReady] = useState(false)
  const [isSplashAnimationComplete, setAnimationComplete] = useState(false)

  useEffect(() => {
    if (isAppReady) {
      Animated.timing(animation, {
        toValue: 0,
        duration: 1000,
        useNativeDriver: true,
      }).start(() => setAnimationComplete(true))
    }
  }, [isAppReady, animation])

  const onImageLoaded = useCallback(async () => {
    try {
      await SplashScreen.hideAsync()
      // Load stuff
      await Promise.all([])
    } catch (e) {
      console.error(e)
      // handle errors
    } finally {
      setAppReady(true)
    }
  }, [])

  return (
    <View style={{ flex: 1 }}>
      {isAppReady && children}
      {!isSplashAnimationComplete && (
        <Animated.View
          pointerEvents="none"
          style={{
            ...Style.wrapper,
            opacity: animation,
          }}
        >
          <Animated.Image
            style={{
              ...Style.logo,
              transform: [{ scale: animation }],
            }}
            source={image}
            onLoadEnd={onImageLoaded}
            fadeDuration={0}
          />
        </Animated.View>
      )}
    </View>
  )
}

export default AnimatedSplashScreen
