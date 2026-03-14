import Picker from "@presentation/components/Picker/Picker"
import { useGetLegalFormsQuery } from "@presentation/features/shared/api"
import { FunctionComponent, useMemo } from "react"
import { Pressable } from "react-native"
import { ProgressBar, RadioButton, TextInput, useTheme } from "react-native-paper"

import Style from "./LegalFormPickerStyle"

interface LegalFormPickerProps {
  title: string
  value?: number
  disabled?: boolean
  placeholder?: string
  onValueChange: (value: number) => void
}

const LegalFormPicker: FunctionComponent<LegalFormPickerProps> = ({
  title,
  onValueChange,
  value,
  placeholder,
  disabled = false,
}) => {
  const { colors } = useTheme()
  const { data = [], isLoading } = useGetLegalFormsQuery()

  const textValue = useMemo(() => data.find((i) => i.id === value)?.title || "", [data, value])

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
            placeholder={placeholder}
            right={
              <TextInput.Icon icon={visible ? "chevron-up" : "chevron-down"} onPress={showDialog} />
            }
            value={textValue}
          />
          {isLoading && (
            <ProgressBar
              indeterminate
              style={[
                Style.progressBar,
                {
                  backgroundColor: colors.outlineVariant,
                },
              ]}
            />
          )}
        </Pressable>
      )}
    >
      <RadioButton.Group
        onValueChange={(i) => onValueChange(parseInt(i))}
        value={value?.toString() || ""}
      >
        {data.map((i) => (
          <RadioButton.Item key={`legalForm_${i.id}`} label={i.title} value={i.id.toString()} />
        ))}
      </RadioButton.Group>
    </Picker>
  )
}

export default LegalFormPicker
