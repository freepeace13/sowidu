import Picker from "@presentation/components/Picker/Picker"
import { useMemo } from "react"
import { Pressable } from "react-native"
import { RadioButton, TextInput } from "react-native-paper"

interface Props {
  title: string
  value: any
  disabled?: boolean
  placeholder?: string
  onValueChange: (value: any) => void
}

function GenderPicker(props: Props) {
  const { title, onValueChange, value, placeholder, disabled = false } = props
  const textValue = useMemo(() => (value === "male" ? "Male" : "Female"), [value])
  return (
    <Picker
      title={title}
      anchor={({ showDialog, visible }) => (
        <Pressable onPress={showDialog}>
          <TextInput
            label={title}
            mode="outlined"
            editable={false}
            disabled={disabled}
            placeholder={placeholder ? placeholder : title}
            value={textValue}
            right={
              <TextInput.Icon
                icon={visible ? "chevron-up" : "chevron-down"}
                forceTextInputFocus={false}
                onPress={(event) => {
                  event.stopPropagation()
                  showDialog()
                }}
              />
            }
          />
        </Pressable>
      )}
    >
      <RadioButton.Group onValueChange={onValueChange} value={value}>
        <RadioButton.Item label={"Male"} value={"male"} />
        <RadioButton.Item label={"Female"} value={"female"} />
      </RadioButton.Group>
    </Picker>
  )
}

export default GenderPicker
