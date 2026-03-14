import { BottomSheetFooter } from "@gorhom/bottom-sheet"
import BottomSheetMenuLayout, {
  BottomSheetMenuFooterProps,
  Props as BottomSheetMenuLayoutPropsType,
} from "@presentation/components/BottomSheetMenu/BottomSheetMenuLayout"
import { useCallback } from "react"
import { Button, useTheme } from "react-native-paper"

import DarkModeChoices from "./DarkModeChoices"
import Style from "./DarkModeStyle"

interface Props extends Pick<BottomSheetMenuLayoutPropsType, "children"> {}

function DarkModeBottomSheet(props: Props) {
  const { colors } = useTheme()

  const renderFooter = useCallback(
    (footerProps: BottomSheetMenuFooterProps) => (
      <BottomSheetFooter {...footerProps} style={Style.footer}>
        <Button
          mode="contained-tonal"
          buttonColor={colors.surface}
          textColor={colors.primary}
          onPress={footerProps.onDismiss}
          theme={{ colors: { outline: colors.surfaceDisabled } }}
        >
          Done
        </Button>
      </BottomSheetFooter>
    ),
    [colors]
  )

  return (
    <BottomSheetMenuLayout
      title="Dark Mode"
      titleIcon="theme-light-dark"
      footerComponent={renderFooter}
      content={(contentProps) => <DarkModeChoices {...contentProps} />}
      {...props}
    />
  )
}

export default DarkModeBottomSheet
