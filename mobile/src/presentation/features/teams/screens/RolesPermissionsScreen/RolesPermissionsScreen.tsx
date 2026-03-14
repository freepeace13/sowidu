import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { View } from "react-native"
import { Appbar, Divider, useTheme } from "react-native-paper"

import {
  GroupedPermissions,
  RolePicker,
  CreateRoleFormDialogProvider,
} from "@presentation/features/teams/components"
import { ReactNode, useMemo, useState } from "react"
import { useGetPermissionsQuery } from "@presentation/features/shared/api"

import Style from "./RolesPermissionsScreenStyle"
import { useGetRoleDetailsQuery, useUpdateRolePermissionsMutation } from "../../teamsApi"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { useCreateRoleFormDialog } from "../../components/CreateRoleFormDialog"
import { withoutFounder } from "@presentation/utils/permissions"
import { StackNavigationProp } from "@react-navigation/stack"

const RolesPermissionsScreenLayout: React.FC<{ children: ReactNode }> = ({ children }) => {
  const { colors } = useTheme()
  const { onPrompt } = useCreateRoleFormDialog()
  const navigation = useNavigation<StackNavigationProp<ParamListBase>>()
  return (
    <ScreenContainer>
      <ScreenHeader
        title="Manage Access"
        background={colors.background}
        onGoBack={navigation.goBack}
        canGoBack={navigation.canGoBack()}
        right={<Appbar.Action icon="plus" color={colors.secondary} onPress={onPrompt} />}
      />
      <View style={Style.content}>{children}</View>
    </ScreenContainer>
  )
}

function RolesPermissionsScreen() {
  const { currentTeam } = useAccount()
  const flashMessage = useFlashMessage()
  const [roleId, setRoleId] = useState<string | string[]>()
  const [updatePermissions] = useUpdateRolePermissionsMutation()
  const { data: permissions = [] } = useGetPermissionsQuery()
  const { data: role } = useGetRoleDetailsQuery({ teamId: currentTeam?.id, roleId } as any, {
    skip: !roleId || !currentTeam?.id,
  })

  const rolePermissions = useMemo(() => {
    if (role?.permissions) {
      return role.permissions
        .filter(({ hasDirectPermission }) => hasDirectPermission)
        .map(({ name }) => name)
    }
    return []
  }, [role])

  const togglePermission = async (permission: string, value: boolean) => {
    if (role?.permissions) {
      const updatedPermissions = role.permissions
        .map((i) => ({ ...i, value: i.name === permission ? value : i.hasDirectPermission }))
        .filter(({ value }) => value)
        .map(({ id }) => id)

      try {
        await updatePermissions({
          teamId: currentTeam?.id,
          roleId,
          permissions: updatedPermissions,
        } as any).unwrap()
      } catch (e) {
        console.error(e)
        flashMessage.showMessage("Update permission failed")
      }
    }
  }

  const groupedPermissions = permissions.map((group) => ({
    title: group.label,
    data: group.permissions,
  }))

  if (!currentTeam) {
    return null
  }

  return (
    <CreateRoleFormDialogProvider teamId={currentTeam.id}>
      <RolesPermissionsScreenLayout>
        <View style={Style.rolePicker}>
          <RolePicker
            teamId={currentTeam.id}
            title="Select role"
            label="Role"
            value={roleId}
            filter={withoutFounder}
            onValueChange={setRoleId}
          />
        </View>
        <Divider />
        <GroupedPermissions
          groups={groupedPermissions}
          disabled={!roleId}
          permissions={rolePermissions}
          onPermissionChange={togglePermission}
        />
      </RolesPermissionsScreenLayout>
    </CreateRoleFormDialogProvider>
  )
}

export default RolesPermissionsScreen
