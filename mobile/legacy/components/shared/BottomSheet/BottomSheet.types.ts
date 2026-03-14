import {
  BottomSheetFlatList,
  BottomSheetScrollView,
  BottomSheetTextInput,
  BottomSheetView,
} from "@gorhom/bottom-sheet"
import { BackdropPressBehavior } from "@gorhom/bottom-sheet/lib/typescript/components/bottomSheetBackdrop/types"
import type {
  BottomSheetFlatListProps,
  BottomSheetScrollViewProps,
} from "@gorhom/bottom-sheet/lib/typescript/components/bottomSheetScrollable/types"
import { BottomSheetTextInputProps } from "@gorhom/bottom-sheet/lib/typescript/components/bottomSheetTextInput"
import { BottomSheetViewProps } from "@gorhom/bottom-sheet/lib/typescript/components/bottomSheetView/types"

export type IBottomSheetProps = {
  isOpen: boolean
  onClose: () => void
  onBackdropPress?: () => void
  backdropPressBehavior?: BackdropPressBehavior
  children: React.ReactNode
  enableOverDrag?: boolean
  enableBackdropTouch?: boolean
  enableHandlePanningGesture?: boolean
  enableContentPanningGesture?: boolean
  enableDynamicSizing?: boolean
  snapPoints?: (string | number)[]
  footerComponent?: any
}

export type BottomSheetComponent = ((props: IBottomSheetProps) => JSX.Element) & {
  FlatList: <T>(props: BottomSheetFlatListProps<T>) => ReturnType<typeof BottomSheetFlatList>
  ScrollView: (props: BottomSheetScrollViewProps) => ReturnType<typeof BottomSheetScrollView>
  TextInput: (props: BottomSheetTextInputProps) => ReturnType<typeof BottomSheetTextInput>
  View: (props: BottomSheetViewProps) => ReturnType<typeof BottomSheetView>
}
