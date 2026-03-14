import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { useProfile } from "@presentation/features/account/hooks/useProfile"
import { DrawerNavigationProp } from "@react-navigation/drawer"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { FunctionComponent, useState } from "react"
import { View } from "react-native"
import { Appbar, Avatar, FAB, List, Modal, Portal, Surface, useTheme } from "react-native-paper"

import Style from "./PeopleScreenStyle"
import People from "../../components/People/People"
import Organizations from "../../components/Organizations/Organizations"
import NewOrganizationForm from "../../components/Organizations/NewOrganizationForm"
import { KeyboardAwareScrollView } from "react-native-keyboard-controller"
import NewPersonForm from "../../components/People/NewPersonForm"
import { Routes } from "@presentation/routes/routes"

interface PeopleScreenProps {
  //
}

const PeopleScreen: FunctionComponent<PeopleScreenProps> = () => {
  const { colors } = useTheme()
  const profile = useProfile()
  const [isAddingOrganization, setIsAddingOrganization] = useState(false)
  const [isAddingPerson, setIsAddingPerson] = useState(false)
  const [isOpen, setIsOpen] = useState(false)
  const navigation = useNavigation<DrawerNavigationProp<ParamListBase>>()

  const stopAddingOrganization = () => {
    setIsAddingOrganization(false)
  }

  const stopAddingPerson = () => {
    setIsAddingPerson(false)
  }

  return (
    <ScreenContainer>
      <ScreenHeader
        title="Address Book"
        background={colors.inverseOnSurface}
        right={<Appbar.Action icon="cog" onPress={console.log} />}
        left={
          <Appbar.Action
            isLeading
            size={32}
            icon={(iconProps) => (
              <Avatar.Image
                {...iconProps}
                source={{
                  uri: profile.avatar,
                }}
              />
            )}
            onPress={navigation.openDrawer}
          />
        }
      />
      <View style={Style.container}>
        <People
          onPress={() => {
            navigation.navigate(Routes.AddressBookProfileScreen)
          }}
        />
        <Organizations
          onPress={() => {
            navigation.navigate(Routes.AddressBookProfileScreen)
          }}
        />
        <Portal>
          {/* Add "organization" modal */}
          <Modal visible={isAddingOrganization} onDismiss={stopAddingOrganization}>
            <Surface style={{ height: "100%", backgroundColor: colors.background }}>
              <Appbar.Header mode="small">
                <Appbar.BackAction onPress={stopAddingOrganization} />
                <Appbar.Content title="New Organization" />
              </Appbar.Header>
              <List.Subheader>People</List.Subheader>
              <KeyboardAwareScrollView
                style={{ flex: 1 }}
                contentContainerStyle={{
                  flex: 1,
                  justifyContent: "flex-end",
                  padding: 12,
                }}
              >
                <NewOrganizationForm />
              </KeyboardAwareScrollView>
            </Surface>
          </Modal>
          {/* Add "person" modal */}
          <Modal visible={isAddingPerson} onDismiss={stopAddingPerson}>
            <Surface style={{ height: "100%", backgroundColor: colors.background }}>
              <Appbar.Header mode="small" elevated>
                <Appbar.BackAction onPress={stopAddingPerson} />
                <Appbar.Content title="New Person" />
              </Appbar.Header>
              <List.Subheader>People</List.Subheader>
              <KeyboardAwareScrollView
                style={{ flex: 1 }}
                contentContainerStyle={{
                  flex: 1,
                  justifyContent: "flex-end",
                  padding: 12,
                }}
              >
                <NewPersonForm />
              </KeyboardAwareScrollView>
            </Surface>
          </Modal>
        </Portal>
        <FAB.Group
          open={isOpen}
          visible
          actions={[
            {
              icon: "account",
              label: "New Person",
              onPress: () => setIsAddingPerson(true),
              size: "small",
              color: colors.surfaceVariant,
              style: {
                backgroundColor: colors.primary,
              },
            },
            {
              icon: "domain",
              label: "New Organization",
              onPress: () => setIsAddingOrganization(true),
              size: "small",
              color: colors.surfaceVariant,
              style: {
                backgroundColor: colors.primary,
              },
            },
          ]}
          icon={isOpen ? "close" : "plus"}
          variant="primary"
          onStateChange={({ open }) => {
            setIsOpen(open)
          }}
          style={{
            position: "absolute",
            margin: 16,
            right: 0,
            bottom: 0,
          }}
          theme={{
            colors: {
              primaryContainer: colors.primary,
              onPrimaryContainer: colors.surfaceVariant,
            },
          }}
        />
      </View>
    </ScreenContainer>
  )
}

export default PeopleScreen
