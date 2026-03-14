import { CustomFormField } from "@presentation/components/CustomForm"
import CustomForm, { FormRef } from "@presentation/components/CustomForm/CustomForm"
import EmailInput from "@presentation/components/EmailInput/EmailInput"
import { FunctionComponent, useRef, useState } from "react"
import { View } from "react-native"
import { KeyboardAwareScrollView } from "react-native-keyboard-controller"
import {
  Appbar,
  HelperText,
  List,
  Modal,
  Portal,
  Surface,
  TextInput,
  useTheme,
} from "react-native-paper"

interface NewOrganizationFormProps {
  //
}

const NewOrganizationForm: FunctionComponent<NewOrganizationFormProps> = () => {
  const { colors } = useTheme()
  const formRef = useRef<FormRef>(null)

  const handleSubmit = async (data: any) => {
    console.log(data)
  }

  return (
    <View style={{ flex: 1 }}>
      <CustomForm ref={formRef} onSubmit={handleSubmit}>
        <CustomFormField
          name="name"
          render={({ value, onChange, error }) => (
            <>
              <TextInput
                label="Name"
                mode="outlined"
                disabled={false}
                activeOutlineColor={colors.primary}
                outlineColor="#71787D"
                textColor="#40484C"
                value={value}
                onChangeText={onChange}
              />
              {error && <HelperText type="error">{error}</HelperText>}
            </>
          )}
        />
      </CustomForm>
    </View>
  )
}

export default NewOrganizationForm
