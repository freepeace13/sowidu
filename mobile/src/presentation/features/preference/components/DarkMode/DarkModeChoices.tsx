import { useAppDispatch, useAppSelector } from "@presentation/app/store/hooks"
import { ContentProps } from "@presentation/components/BottomSheetMenu/BottomSheetMenuLayout"
import { RadioButton } from "react-native-paper"

import { Theme, changeTheme, selectTheme } from "../../preferenceSlice"

interface Props extends ContentProps {}

function DarkModeChoices(props: Props) {
  const dispatch = useAppDispatch()
  const currentTheme = useAppSelector(selectTheme)
  const onChangeTheme = (theme: string) => {
    dispatch(changeTheme(theme as Theme))
  }
  return (
    <RadioButton.Group onValueChange={onChangeTheme} value={currentTheme}>
      <RadioButton.Item label="On" value="dark" />
      <RadioButton.Item label="Off" value="light" />
      <RadioButton.Item label="System Defaults" value="auto" />
    </RadioButton.Group>
  )
}

export default DarkModeChoices
