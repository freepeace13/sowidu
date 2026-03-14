import React from "react"
import { ListItemProps, List } from "react-native-paper"

import { useBottomSheetMenuContext } from "./BottomSheetMenuContext"

export default function BottomSheetMenuItem(props: ListItemProps) {
  const { options, close } = useBottomSheetMenuContext()

  const onPress = (e) => {
    props.onPress && props.onPress(e)
    options.closeOnClick && close()
  }

  return <List.Item {...props} onPress={onPress} />
}
