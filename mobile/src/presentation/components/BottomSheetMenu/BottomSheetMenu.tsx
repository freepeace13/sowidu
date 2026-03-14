import {
  BottomSheetModal,
  BottomSheetView,
  BottomSheetBackdrop,
  BottomSheetModalProps,
  BottomSheetHandle,
  BottomSheetProps,
  BottomSheetHandleProps,
  BottomSheetBackdropProps,
} from "@gorhom/bottom-sheet"
import React, { ReactNode, forwardRef, useCallback, useMemo } from "react"
import { useTheme } from "react-native-paper"
import { useSafeAreaInsets } from "react-native-safe-area-context"

import Style from "./BottomSheetMenuStyle"
import BottomSheetMenuTitle from "./BottomSheetMenuTitle"

export interface BottomSheetMenuProps
  extends Pick<BottomSheetModalProps, "stackBehavior">,
    Pick<BottomSheetProps, "footerComponent"> {
  onStateChange?: (opened: boolean) => void
  onPresent?: () => void
  onDismiss?: () => void
  title?: string | React.JSX.Element
  titleIcon?: string
  contentContainerStyle?: any
  children: ReactNode
}

const BottomSheetMenu = forwardRef<BottomSheetModal, BottomSheetMenuProps>(
  (
    {
      onStateChange,
      onPresent,
      onDismiss,
      title,
      titleIcon,
      stackBehavior,
      footerComponent,
      children,
    },
    ref
  ) => {
    const { colors } = useTheme()
    const { bottom: safeBottomArea } = useSafeAreaInsets()

    const renderHandleComponent = useCallback(
      (props: BottomSheetHandleProps) => (
        <BottomSheetHandle {...props}>
          {title ? (
            typeof title === "string" ? (
              <BottomSheetMenuTitle title={title} icon={titleIcon} />
            ) : (
              title
            )
          ) : null}
        </BottomSheetHandle>
      ),
      [title, titleIcon]
    )

    const renderBackdrop = useCallback(
      (props: BottomSheetBackdropProps) => (
        <BottomSheetBackdrop
          {...props}
          enableTouchThrough
          pressBehavior="close"
          appearsOnIndex={0}
          disappearsOnIndex={-1}
        />
      ),
      []
    )

    const handleIndexChange = useCallback(
      (index: number) => {
        const isOpen = index !== -1

        if (isOpen && onPresent) {
          onPresent()
        }

        if (onStateChange) {
          onStateChange(isOpen)
        }
      },
      [onStateChange, onPresent]
    )

    const contentContainerStyle = useMemo(
      () => ({
        ...Style.contentContainerStyle,
        backgroundColor: "#fbfcfe",
        paddingBottom: safeBottomArea || 6,
      }),
      [safeBottomArea]
    )

    return (
      <BottomSheetModal
        ref={ref}
        onDismiss={onDismiss}
        onChange={handleIndexChange}
        enableDynamicSizing
        handleIndicatorStyle={{ backgroundColor: colors.outlineVariant }}
        enableContentPanningGesture={false}
        enablePanDownToClose
        stackBehavior={stackBehavior}
        handleComponent={renderHandleComponent}
        footerComponent={footerComponent}
        backdropComponent={renderBackdrop}
      >
        <BottomSheetView
          enableFooterMarginAdjustment={!!footerComponent}
          style={[{ ...contentContainerStyle }, contentContainerStyle]}
        >
          {children}
        </BottomSheetView>
      </BottomSheetModal>
    )
  }
)

BottomSheetMenu.displayName = "BottomSheetMenu"

export default BottomSheetMenu
