import { BottomSheetFooterProps, BottomSheetModal } from "@gorhom/bottom-sheet"
import React, { useRef } from "react"

import BottomSheetMenu, { Props as BottomSheetMenuPropsType } from "./BottomSheetMenu"

export interface ContentProps {
  onDismiss: () => void
}

export interface ChildrenProps {
  onPresent: () => void
}

export interface BottomSheetMenuFooterProps
  extends BottomSheetFooterProps,
    ChildrenProps,
    ContentProps {}

export interface Props extends Omit<BottomSheetMenuPropsType, "footerComponent"> {
  footerComponent?: React.FC<BottomSheetMenuFooterProps>
  content: (props: ContentProps) => React.JSX.Element
  children: (props: ChildrenProps) => React.JSX.Element
}

export default function BottomSheetMenuLayout(props: Props) {
  const { children, content, footerComponent, ...restProps } = props
  const ref = useRef<BottomSheetModal>(null)

  const onPresent = () => {
    ref.current && ref.current.present()
  }

  const onDismiss = () => {
    ref.current && ref.current.dismiss()
  }

  const renderFooter = footerComponent
    ? (footerProps: BottomSheetFooterProps) =>
        footerComponent({ ...footerProps, onDismiss, onPresent })
    : undefined

  return (
    <>
      <BottomSheetMenu {...restProps} footerComponent={renderFooter} ref={ref}>
        {content({ onDismiss })}
      </BottomSheetMenu>
      {props.children({ onPresent })}
    </>
  )
}
