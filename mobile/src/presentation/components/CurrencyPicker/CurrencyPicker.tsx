import Picker from "@presentation/components/Picker/Picker"
import { FunctionComponent } from "react"
import { Pressable } from "react-native"
import { RadioButton, TextInput } from "react-native-paper"
import { Currency, CurrencyCode } from "@domain/shared/types"

const currencies = Object.keys(Currency) as CurrencyCode[]

interface CurrencyPickerProps {
  title?: string
  value?: string
  disabled?: boolean
  readonly?: boolean
  placeholder?: string
  onValueChange: (value: CurrencyCode) => void
}

const CurrencyPicker: FunctionComponent<CurrencyPickerProps> = ({
  value = Currency.EUR,
  title = "Currency",
  disabled = false,
  readonly = false,
  placeholder,
  onValueChange,
}) => {
  const handleValueChange = (value: string) => onValueChange(value as CurrencyCode)
  return (
    <Picker
      title={title}
      anchor={({ showDialog, visible }) => (
        <Pressable onPress={() => !readonly && showDialog()}>
          <TextInput
            label={title}
            mode="outlined"
            editable={!disabled}
            readOnly={readonly}
            disabled={disabled}
            placeholder={placeholder ? placeholder : title}
            value={value}
            right={
              <TextInput.Icon
                icon={visible ? "chevron-up" : "chevron-down"}
                forceTextInputFocus={false}
                disabled={readonly}
                onPress={(event) => {
                  event.stopPropagation()
                  if (!readonly) {
                    showDialog()
                  }
                }}
              />
            }
          />
        </Pressable>
      )}
    >
      <RadioButton.Group onValueChange={handleValueChange} value={value}>
        {currencies.map((i) => (
          <RadioButton.Item key={i} label={i} value={i} />
        ))}
      </RadioButton.Group>
    </Picker>
  )
}

export default CurrencyPicker
