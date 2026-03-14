import { useProfile } from "@presentation/features/account/hooks/useProfile"
import { StyleSheet, View } from "react-native"
import { Avatar, Icon, Text, TouchableRipple, useTheme } from "react-native-paper"

interface Props {
  onPress?: () => void
}

function AccountSummary(props: Props) {
  const { colors } = useTheme()
  const profile = useProfile()
  return (
    <View style={Style.container}>
      <TouchableRipple onPress={props.onPress}>
        <View style={Style.wrapper}>
          <Avatar.Image size={36} source={{ uri: profile.avatar }} />
          <View style={Style.content}>
            <View style={Style.label}>
              <Text numberOfLines={1} variant="labelLarge">
                {profile.name}
              </Text>
              <Text
                numberOfLines={1}
                variant="labelSmall"
                style={[Style.subtitle, { color: colors.outline }]}
              >
                Lorem ipsum
              </Text>
            </View>
            <Icon source="sync" size={20} color={colors.primary} />
          </View>
        </View>
      </TouchableRipple>
    </View>
  )
}

const Style = StyleSheet.create({
  container: {
    borderRadius: 4,
    marginHorizontal: 10,
    marginVertical: 4,
    overflow: "hidden",
  },
  wrapper: {
    alignItems: "center",
    flexDirection: "row",
    padding: 8,
  },
  content: {
    marginLeft: 20,
    flex: 1,
    alignItems: "center",
    flexDirection: "row",
    justifyContent: "space-between",
  },
  label: {
    flexDirection: "column",
    flex: 1,
    marginRight: 12,
  },
  subtitle: {
    textAlign: "left",
  },
})

export default AccountSummary
