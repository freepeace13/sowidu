import BottomSheet, { BottomSheetBackdrop } from "@gorhom/bottom-sheet"
import pick from "lodash/pick"
import React, { useState, PropsWithChildren } from "react"
import { Portal } from "react-native-paper"

import { BottomSheetMenuContext } from "./BottomSheetMenuContext"

interface MenuTriggerProps {
  readonly opened: boolean
  readonly onPress: () => void
}

interface BottomSheetMenuProps extends PropsWithChildren {
  trigger?: (props: MenuTriggerProps) => React.ReactNode | undefined
  height?: number | string
  opened?: boolean
  close?: () => void
  closeOnClick?: boolean
}

export default function BottomSheetMenu(props: BottomSheetMenuProps) {
  const { trigger, height = "60%", closeOnClick = true, children, ...rest } = props
  const [isOpen, setIsOpen] = useState(false)

  const { opened, close } = pick(
    {
      opened: isOpen,
      close: () => setIsOpen(false),
      ...rest,
    },
    ["opened", "close"],
  )

  return (
    <>
      {trigger &&
        trigger({
          opened,
          onPress: () => setIsOpen(true),
        })}
      {opened && (
        <Portal>
          <BottomSheet
            index={0}
            snapPoints={[height]}
            enableDynamicSizing={false}
            enablePanDownToClose={false}
            enableOverDrag={false}
            enableHandlePanningGesture={false}
            enableContentPanningGesture={false}
            backdropComponent={(props) => (
              <BottomSheetBackdrop
                {...props}
                enableTouchThrough
                pressBehavior={0}
                onPress={close}
                appearsOnIndex={0}
                disappearsOnIndex={-1}
              />
            )}
          >
            <BottomSheetMenuContext.Provider
              value={{
                opened,
                close,
                options: {
                  height,
                  closeOnClick,
                },
              }}
            >
              {children}
            </BottomSheetMenuContext.Provider>
          </BottomSheet>
        </Portal>
      )}
    </>
  )
}
