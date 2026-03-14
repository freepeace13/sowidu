import { SectionGroup } from "@presentation/components/SectionGroup"
import { createContext, FunctionComponent, useContext, useMemo, useState } from "react"
import { View } from "react-native"
import debounce from "lodash/debounce"
import {
  ActivityIndicator,
  Avatar,
  Button,
  Card,
  Divider,
  HelperText,
  Icon,
  Text,
  TextInput,
  TouchableRipple,
  useTheme,
} from "react-native-paper"
import { RolePicker } from "../RolePicker"
import { KeyboardAwareScrollView } from "react-native-keyboard-controller"
import { ActiveStatus } from "@presentation/components"

import Style from "./MemberProfileStyle"
import { useGetMembersInfoQuery, useUpdateMembersInfoMutation } from "../../teamsApi"
import { Member } from "@domain/teams/members/Member"
import { withoutFounder } from "@presentation/utils/permissions"
import { getValidationErrorMessage } from "@application/services/helpers"
import CurrencyPicker from "@presentation/components/CurrencyPicker/CurrencyPicker"
import { CurrencyCode } from "@domain/shared/types"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import { useAuthorization } from "@presentation/features/account/hooks/useAuthorization"

interface MemberProfileContextType {
  info: Member
}

const MemberProfileContext = createContext<MemberProfileContextType>({} as MemberProfileContextType)

interface MemberProfileProps {
  teamId: number
  memberId: number
}

const MemberProfile: FunctionComponent<MemberProfileProps> = ({ teamId, memberId }) => {
  const { colors } = useTheme()
  const { data: info, isLoading } = useGetMembersInfoQuery({ teamId, memberId })

  const isNotReady = !info || isLoading

  if (isNotReady) {
    return (
      <View style={Style.loading}>
        <ActivityIndicator color={colors.primary} size={25} />
      </View>
    )
  }

  return (
    <MemberProfileContext.Provider value={{ info }}>
      <KeyboardAwareScrollView style={Style.keyboard}>
        <View style={Style.overview}>
          <Avatar.Image source={{ uri: info.userInfo.photoURL }} size={70} />
          <View style={Style.summary}>
            <View style={Style.summaryContent}>
              <Text variant="titleMedium">{info.userInfo.name}</Text>
              <Text variant="bodyMedium">{info.role}</Text>
            </View>
            <ActiveStatus value={new Date()} />
          </View>
          <Button mode="contained">View Profile</Button>
        </View>
        <View style={Style.tabs}>
          <TabButton icon="message-text" text="Chat" onPress={console.log} />
          <TabButton icon="account-plus" text="Add Address Book" onPress={console.log} />
          <TabButton icon="content-copy" text="Copy" onPress={console.log} />
        </View>
        <Divider />
        <View style={Style.content}>
          <SectionGroup title="Information" icon="information" outlined={false}>
            <SectionContent icon="map-marker" title="Address" content="-" />
            <SectionContent icon="phone" title="Contact Number" content="-" />
            <SectionContent icon="email" title="Email" content={info.userInfo.email} />
          </SectionGroup>
          <SectionGroup title="Security" icon="security" outlined={false}>
            <Roles />
          </SectionGroup>
          <SectionGroup title="Rates" icon="cash-multiple" outlined={false}>
            <Rates />
          </SectionGroup>
        </View>
      </KeyboardAwareScrollView>
    </MemberProfileContext.Provider>
  )
}

const Rates: FunctionComponent<any> = () => {
  const { user } = useAccount()
  const { info } = useContext(MemberProfileContext)
  const [updateMemberInfo, { isLoading, error }] = useUpdateMembersInfoMutation()
  const [rate, setRate] = useState(info.rates?.rate?.toString() || "")
  const [currency, setCurrency] = useState(info.rates?.currency || "EUR")
  const { doesntHavePermissionTo } = useAuthorization(user)
  const flashMessage = useFlashMessage()

  const isReadOnly = useMemo(
    () => info.ownsTeam || doesntHavePermissionTo("can manage employee rates"),
    [info, doesntHavePermissionTo]
  )

  const updateRates = debounce(async () => {
    const { teamId, memberId } = info
    try {
      await updateMemberInfo({
        teamId,
        memberId,
        rates: {
          currency,
          rate,
        },
      }).unwrap()
      flashMessage.showMessage("Updated.")
    } catch (error) {
      console.log(error)
    }
  }, 300)

  const renderError = (name: string) => {
    const errorMessage = getValidationErrorMessage(error, name)
    return errorMessage && <HelperText type="error">{errorMessage}</HelperText>
  }

  const handleCurrencyChange = (value: CurrencyCode) => {
    setCurrency(value)
    requestAnimationFrame(updateRates)
  }

  return (
    <>
      <View>
        <CurrencyPicker
          placeholder="Select Currency"
          disabled={isLoading}
          readonly={isReadOnly}
          value={currency}
          onValueChange={handleCurrencyChange}
        />
        {renderError("rates.currency")}
      </View>
      <View>
        <TextInput
          label="Amount"
          mode="outlined"
          disabled={isLoading}
          readOnly={isReadOnly}
          value={rate}
          onChangeText={setRate}
          onBlur={updateRates}
          left={<TextInput.Icon icon="currency-eur" />}
        />
        {renderError("rates.rate")}
      </View>
    </>
  )
}

const Roles: FunctionComponent<any> = () => {
  const flashMessage = useFlashMessage()
  const { user } = useAccount()
  const { info } = useContext(MemberProfileContext)
  const [roles, setRoles] = useState(info.teamRoles)
  const [updateMemberInfo, { isLoading, error }] = useUpdateMembersInfoMutation()
  const { doesntHavePermissionTo } = useAuthorization(user)

  const isReadOnly = useMemo(
    () => info.ownsTeam || doesntHavePermissionTo("manage permissions"),
    [info, doesntHavePermissionTo]
  )

  const errorMessage = getValidationErrorMessage(error, "roles")

  const handleRoleChange = async (roles: string[]) => {
    setRoles(roles)
    const { teamId, memberId } = info
    try {
      await updateMemberInfo({ teamId, memberId, roles }).unwrap()
      flashMessage.showMessage("Updated.")
    } catch (err) {
      console.log(err)
    }
  }

  return (
    <>
      <RolePicker
        teamId={info.teamId}
        title="Roles & Permissions"
        label="Role"
        multiple
        value={roles}
        disabled={isLoading}
        readonly={isReadOnly}
        filter={withoutFounder}
        valueExtractor={(item) => item.name}
        onValueChange={handleRoleChange}
        left={<TextInput.Icon icon="account-tie" />}
      />
      {errorMessage && <HelperText type="error">{errorMessage}</HelperText>}
    </>
  )
}

interface TabButtonProps {
  icon: any
  text: string
  onPress: () => void
}

const TabButton: FunctionComponent<TabButtonProps> = ({ icon, text, onPress, ...restProps }) => {
  return (
    <TouchableRipple style={Style.tabButton} onPress={onPress}>
      <View style={{ alignItems: "center", gap: 6 }}>
        <Icon source={icon} size={32} />
        <Text variant="labelMedium">{text}</Text>
      </View>
    </TouchableRipple>
  )
}

const SectionContent: FunctionComponent<{
  icon: any
  title: string
  content: string
}> = ({ icon, title, content }) => {
  return (
    <Card.Title
      title={title}
      subtitle={content}
      titleNumberOfLines={1}
      titleVariant="bodyMedium"
      subtitleVariant="titleMedium"
      subtitleNumberOfLines={3}
      left={() => <Icon source={icon} size={26} />}
    />
  )
}

export default MemberProfile
