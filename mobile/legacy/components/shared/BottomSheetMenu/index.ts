import { List } from "react-native-paper"

import BottomSheetMenuMain from "./BottomSheetMenu"
import BottomSheetMenuContent from "./BottomSheetMenuContent"
import BottomSheetMenuItem from "./BottomSheetMenuItem"

const BottomSheetMenuTemp: any = BottomSheetMenuMain
BottomSheetMenuTemp.Content = BottomSheetMenuContent
BottomSheetMenuTemp.Item = BottomSheetMenuItem
BottomSheetMenuTemp.Icon = List.Icon
BottomSheetMenuTemp.Section = List.Section

const BottomSheetMenu = BottomSheetMenuTemp as typeof BottomSheetMenuMain & {
  Content: typeof BottomSheetMenuContent
  Item: typeof BottomSheetMenuItem
  Icon: typeof List.Icon
  Section: typeof List.Section
}

export default BottomSheetMenu
