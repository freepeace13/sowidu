import React, { PropsWithChildren } from "react"
import { FlexStyle, StyleProp, StyleSheet, View, ViewStyle } from "react-native"

import Style from "./style"

interface StackProps {
  space?: number | undefined
  direction?: "column" | "row" | undefined
  style?: StyleProp<ViewStyle> | undefined
}

export default function Stack({
  space = 12,
  direction = "row",
  children,
  style,
}: PropsWithChildren<StackProps>) {
  const childrenArray = React.Children.toArray(children)
  const stackStyle: StyleProp<ViewStyle> = {
    ...StyleSheet.flatten(style),
    ...(direction === "row" && { alignItems: "center" }),
    flexDirection: direction,
  }
  const spacingStyle: object = {
    ...(direction === "row" ? { width: space } : { height: space }),
  }
  return (
    <View style={[Style.stack, stackStyle]}>
      {childrenArray.map((child: any, index: number) => (
        <React.Fragment key={child.key ?? `spaced-child-${index}`}>
          {child}
          {index < childrenArray.length - 1 && <View style={spacingStyle} />}
        </React.Fragment>
      ))}
    </View>
  )
}
