import {
  BottomSheetFlatList,
  BottomSheetScrollView,
  BottomSheetTextInput,
  BottomSheetView,
} from "@gorhom/bottom-sheet"

import BottomSheetMain from "./BottomSheet"
import type { BottomSheetComponent } from "./BottomSheet.types"

const BottomSheetTemp: any = BottomSheetMain
BottomSheetTemp.FlatList = BottomSheetFlatList
BottomSheetTemp.ScrollView = BottomSheetScrollView
BottomSheetTemp.TextInput = BottomSheetTextInput
BottomSheetTemp.View = BottomSheetView

const BottomSheet = BottomSheetTemp as BottomSheetComponent

export default BottomSheet
