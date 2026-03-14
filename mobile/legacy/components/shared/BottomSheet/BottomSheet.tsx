import BottomSheetComponent, { BottomSheetBackdrop } from "@gorhom/bottom-sheet"
import React, { memo, forwardRef, useMemo } from "react"
import { useTheme } from "react-native-paper"

import type { IBottomSheetProps } from "./BottomSheet.types"

function BottomSheet(
  {
    isOpen,
    onClose,
    snapPoints = ["100%"],
    onBackdropPress,
    backdropPressBehavior = "close",
    enableBackdropTouch = true,
    enableHandlePanningGesture = false,
    enableContentPanningGesture = false,
    enableDynamicSizing = true,
    children,
    ...props
  }: IBottomSheetProps,
  ref,
) {
  const theme = useTheme()
  const snapPointIndex = useMemo(() => (isOpen ? 0 : -1), [isOpen])
  return (
    <BottomSheetComponent
      {...props}
      ref={ref}
      index={snapPointIndex}
      snapPoints={snapPoints}
      enableDynamicSizing={enableDynamicSizing}
      enableHandlePanningGesture={enableHandlePanningGesture}
      enableContentPanningGesture={enableContentPanningGesture}
      backgroundStyle={{ backgroundColor: theme.colors.background }}
      backdropComponent={(props) => (
        <BottomSheetBackdrop
          {...props}
          onPress={onBackdropPress ? onBackdropPress : onClose}
          pressBehavior={backdropPressBehavior}
          enableTouchThrough={enableBackdropTouch}
          appearsOnIndex={0}
          disappearsOnIndex={-1}
        />
      )}
    >
      {children}
    </BottomSheetComponent>
  )
}

export default memo(forwardRef(BottomSheet))
