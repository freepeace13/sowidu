import { CustomForm, CustomFormField } from "@presentation/components/CustomForm"
import { FormRef } from "@presentation/components/CustomForm/CustomForm"
import Stack from "@presentation/components/Stack/Stack"
import { FunctionComponent, useRef } from "react"
import { Button, HelperText, TextInput, useTheme } from "react-native-paper"
import { InstitutionPicker } from "../InstitutionPicker"
import { LegalFormPicker } from "../LegalFormPicker"

interface CreateTeamFormProps {
  isLoading?: boolean
  onCreate: (values: any) => Promise<void>
}

const CreateTeamForm: FunctionComponent<CreateTeamFormProps> = (props) => {
  const { colors } = useTheme()
  const formRef = useRef<FormRef>(null)
  return (
    <CustomForm ref={formRef} onSubmit={props.onCreate}>
      <Stack direction="column">
        <CustomFormField
          name="name"
          render={({ value, onChange, error }) => (
            <>
              <TextInput
                label="Name"
                mode="outlined"
                disabled={props.isLoading}
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
        <CustomFormField
          name="institutionTypeId"
          render={({ value, onChange, error }) => (
            <>
              <InstitutionPicker
                title="Institution Type"
                placeholder="Choose"
                value={value}
                onValueChange={onChange}
              />
              {error && <HelperText type="error">{error}</HelperText>}
            </>
          )}
        />
        <CustomFormField
          name="legalFormId"
          render={({ value, onChange, error }) => (
            <>
              <LegalFormPicker title="Legal Form" value={value} onValueChange={onChange} />
              {error && <HelperText type="error">{error}</HelperText>}
            </>
          )}
        />
      </Stack>

      <Button
        onPress={() => formRef.current?.submitForm()}
        loading={props.isLoading}
        disabled={props.isLoading}
        mode="contained"
        buttonColor="#006686"
      >
        Create Organization
      </Button>
    </CustomForm>
  )
}

export default CreateTeamForm
