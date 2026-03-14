import { createStackNavigator } from "@react-navigation/stack"

import IntroductionScreen from "../components/Introduction"
import LastLoginUserScreen from "../components/LastLoginUser"
import LoginWithEmailScreen from "../components/LoginWithEmail"
import RegisterWithEmailScreen from "../components/RegisterWithEmail"
import AuthConstants from "../constants"
import { useUserInfo } from "../hooks"

const AuthStack = createStackNavigator()

const IntroductionRoute = AuthConstants.RouteNames.Introduction
const LoginRoute = AuthConstants.RouteNames.LoginWithEmail
const RegisterRoute = AuthConstants.RouteNames.RegisterWithEmail
const LastLoginUser = AuthConstants.RouteNames.LastLoginUser

export default function AuthNavigator() {
  const userInfo = useUserInfo()
  const initialRouteName = userInfo ? LastLoginUser : IntroductionRoute
  return (
    <AuthStack.Navigator initialRouteName={initialRouteName} screenOptions={{ headerShown: false }}>
      <AuthStack.Screen name={IntroductionRoute} component={IntroductionScreen} />
      <AuthStack.Screen name={LastLoginUser} component={LastLoginUserScreen} />
      <AuthStack.Screen name={LoginRoute} component={LoginWithEmailScreen} />
      <AuthStack.Screen name={RegisterRoute} component={RegisterWithEmailScreen} />
    </AuthStack.Navigator>
  )
}
