import { SectionList } from "react-native"
import { List, Switch, Text } from "react-native-paper"
import * as Str from "@presentation/utils/string"

import Style from "./GroupedPermissionsStyle"

type Props = {
  groups: { title: string; data: string[] }[]
  permissions: string[]
  disabled?: boolean
  onPermissionChange: (item: string, hasDirectPermission: boolean) => void
  emptyMessage?: string
}

export const GroupedPermissions = (props: Props) => {
  const {
    groups,
    permissions,
    onPermissionChange,
    emptyMessage = "No data to show.",
    disabled = false,
  } = props
  return (
    <SectionList
      sections={groups}
      ListEmptyComponent={
        <Text variant="titleSmall" style={{ textAlign: "center" }}>
          {emptyMessage}
        </Text>
      }
      keyExtractor={(item, index) => item + index}
      renderSectionHeader={({ section: { title } }) => <List.Subheader>{title}</List.Subheader>}
      renderItem={({ item, index }) => (
        <List.Item
          title={Str.title(item)}
          titleNumberOfLines={1}
          style={Style.item}
          left={() => (
            <Switch
              disabled={disabled}
              value={permissions.includes(item)}
              onValueChange={(hasDirectPermission) => onPermissionChange(item, hasDirectPermission)}
            />
          )}
        />
      )}
    />
  )
}
