import DarkModeBottomSheet from "@presentation/features/preference/components/DarkMode/DarkModeBottomSheet"
import { DrawerItem } from "@react-navigation/drawer"
import { Icon } from "react-native-paper"

type Props = {
  closeDrawer: () => void
}

function DarkMode(props: Props) {
  return (
    <DarkModeBottomSheet>
      {({ onPresent }) => (
        <DrawerItem
          icon={(props) => <Icon {...props} source="theme-light-dark" />}
          label="Dark Mode"
          onPress={() => {
            props.closeDrawer()
            requestAnimationFrame(onPresent)
          }}
        />
      )}
    </DarkModeBottomSheet>
  )
}

export default DarkMode
