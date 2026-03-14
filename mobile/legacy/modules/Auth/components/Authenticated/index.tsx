import React from "react"
import { View, Image } from "react-native"
import { ActivityIndicator, Text, useTheme } from "react-native-paper"
import { Illustrations, Stack } from "ui-module"

import Style from "./style"
import { useIsAuthenticated } from "../../hooks"
import { AuthNavigator } from "../../navigator"
import { Api as AuthApi } from "../../services"

function FetchingUserInfoIndicator({ children }) {
  const { colors } = useTheme()
  const { isFetching } = AuthApi.useGetUserInfoQuery()

  if (!isFetching) {
    return children
  }

  return (
    <View style={[Style.fetchingIndicator, { backgroundColor: colors.primary }]}>
      <View style={Style.logoContainer}>
        <Image
          source={Illustrations.Images.brandIconWhite}
          style={Style.logo}
          resizeMode="contain"
        />
      </View>
      <Stack direction="row" space={8} style={Style.spinnerContainer}>
        <ActivityIndicator color={colors.surfaceVariant} size={15} />
        <Text variant="labelLarge" style={{ color: colors.surfaceVariant }}>
          Logging In
        </Text>
      </Stack>
    </View>
  )
}

export default function Authenticated({ children }) {
  const isAuthenticated = useIsAuthenticated()
  if (!isAuthenticated) {
    return <AuthNavigator />
  }
  return <FetchingUserInfoIndicator>{children}</FetchingUserInfoIndicator>
}
