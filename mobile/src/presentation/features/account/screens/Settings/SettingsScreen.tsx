import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { NavigationProp, ParamListBase, useNavigation } from "@react-navigation/native"
import { View } from "react-native"
import { Divider, useTheme } from "react-native-paper"

import Style from "./SettingsScreenStyle"
import LinkCard from "@presentation/features/teams/components/LinkCard/LinkCard"
import { Routes } from "@presentation/routes/routes"
import { useAuthorization } from "../../hooks/useAuthorization"
import { useAccount } from "../../hooks/useAccount"

function SettingsScreen() {
  const { colors } = useTheme()
  const { user } = useAccount()
  const navigation = useNavigation<NavigationProp<ParamListBase>>()
  const { hasAnyPermission } = useAuthorization(user)
  return (
    <ScreenContainer>
      <ScreenHeader
        title="Account Settings"
        background={colors.background}
        onGoBack={() => navigation.goBack()}
        canGoBack={navigation.canGoBack()}
      />
      <Divider />
      <View style={Style.content}>
        {hasAnyPermission([
          "update settings",
          "can manage organization settings",
          "access_employees",
          "add member",
        ]) && (
          <LinkCard
            title="Members"
            icon="account-group"
            subtitle="Invite more members and assign their roles."
            onPress={() =>
              navigation.navigate(Routes.TeamSettingsNavigator, {
                screen: Routes.TeamMembersListScreen,
              })
            }
          />
        )}
        {hasAnyPermission(["can manage organization settings", "manage permissions"]) && (
          <LinkCard
            title="Manage Access"
            icon="key-chain"
            subtitle="Invite more members and assign their roles."
            onPress={() =>
              navigation.navigate(Routes.TeamSettingsNavigator, {
                screen: Routes.RolesPermissionsScreen,
              })
            }
          />
        )}
        {hasAnyPermission(["can manage organization settings"]) && (
          <LinkCard
            title="Media Settings"
            icon="cog-play"
            subtitle="Invite more members and assign their roles."
            onPress={console.log}
          />
        )}
        {hasAnyPermission(["can manage organization categories"]) && (
          <LinkCard
            title="Categories"
            icon="shape"
            subtitle="Invite more members and assign their roles."
            onPress={console.log}
          />
        )}
        {hasAnyPermission(["can manage organization settings"]) && (
          <LinkCard
            title="Invoice Settings"
            icon="file-cog"
            subtitle="Invite more members and assign their roles."
            onPress={() => navigation.navigate(Routes.AccountEditProfileScreen)}
          />
        )}
        {hasAnyPermission(["can manage organization settings"]) && (
          <LinkCard
            title="Profile"
            icon="account-circle"
            subtitle="Update profile information"
            onPress={() => navigation.navigate(Routes.AccountEditProfileScreen)}
          />
        )}
      </View>
    </ScreenContainer>
  )
}

export default SettingsScreen
