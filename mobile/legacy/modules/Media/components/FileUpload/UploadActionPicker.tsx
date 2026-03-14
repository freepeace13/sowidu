import { FAB, useTheme } from "react-native-paper"
import { BottomSheetMenu } from "ui-module"

import Style from "./style"

export default function UploadActionPicker({ onCameraPress, onPhotoOrVideoPress, onBrowsePress }) {
  const { colors } = useTheme()

  const renderTrigger = (props) =>
    !props.opened && (
      <FAB
        {...props}
        icon="plus"
        backgroundColor={colors.primary}
        color={colors.onPrimary}
        style={Style.fab}
      />
    )

  return (
    <BottomSheetMenu height={200} trigger={renderTrigger}>
      <BottomSheetMenu.Content>
        <BottomSheetMenu.Section>
          <BottomSheetMenu.Item
            left={(props) => <BottomSheetMenu.Icon {...props} icon="video-image" />}
            title="Photo or Video"
            onPress={onPhotoOrVideoPress}
          />
          <BottomSheetMenu.Item
            left={(props) => <BottomSheetMenu.Icon {...props} icon="camera" />}
            title="Camera"
            onPress={onCameraPress}
          />
          <BottomSheetMenu.Item
            left={(props) => <BottomSheetMenu.Icon {...props} icon="folder-open" />}
            title="Browse"
            onPress={onBrowsePress}
          />
        </BottomSheetMenu.Section>
      </BottomSheetMenu.Content>
    </BottomSheetMenu>
  )
}
