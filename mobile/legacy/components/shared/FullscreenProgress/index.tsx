import { useRef, useState } from "react"
import { View, ActivityIndicator, StyleSheet } from "react-native"
import { AnimatedCircularProgress } from "react-native-circular-progress"
import { useTheme, Avatar, Text } from "react-native-paper"
import { AvatarImageSource } from "react-native-paper/lib/typescript/components/Avatar/AvatarImage"
import { Illustrations } from "ui-module"

import Style from "./style"

interface FullscreenProgressProps {
  animate?: boolean
  text?: string
  image: AvatarImageSource
}

export default function FullscreenProgress({
  animate = false,
  text,
  image,
}: FullscreenProgressProps) {
  const { colors } = useTheme()
  return (
    <View
      style={[
        {
          backgroundColor: colors.primary,
        },
        Style.container,
      ]}
    >
      <View>
        <View
          style={{
            ...StyleSheet.absoluteFillObject,
            alignItems: "center",
            justifyContent: "center",
          }}
        >
          <Avatar.Image source={image} size={44} />
          {/* <Avatar.Image
            source={{
              uri: "https://pbs.twimg.com/profile_images/1694737709166899200/EQkjv0gi_400x400.jpg",
            }}
            size={44}
          /> */}
        </View>
        <ActivityIndicator animating={animate} color={colors.onPrimary} size={75} />
      </View>
      {text && (
        <Text variant="bodyMedium" style={{ color: colors.onPrimary }}>
          {text}
        </Text>
      )}
    </View>
  )
}
