import { BottomSheetScrollView, BottomSheetView } from "@gorhom/bottom-sheet"
import React, { PropsWithChildren } from "react"

interface BottomSheetMenuContentProps extends PropsWithChildren {
  StickyHeading?: React.ReactNode | undefined
}

export default function BottomSheetMenuContent({
  StickyHeading,
  children,
}: BottomSheetMenuContentProps) {
  return (
    <BottomSheetView style={{ flex: 1 }}>
      {StickyHeading}
      <BottomSheetScrollView>{children}</BottomSheetScrollView>
    </BottomSheetView>
  )
}
