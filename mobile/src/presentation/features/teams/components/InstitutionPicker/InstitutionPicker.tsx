import Picker from "@presentation/components/Picker/Picker"
import { FunctionComponent, useMemo } from "react"
import { Pressable } from "react-native"
import { ProgressBar, RadioButton, TextInput, useTheme } from "react-native-paper"
import { useGetInstitutionTypesQuery } from "@presentation/features/shared/api"

import Style from "./InstitutionPickerStyle"

interface InstitutionPickerProps {
  title: string
  value?: number
  placeholder?: string
  disabled?: boolean
  onValueChange: (value: number) => void
}

const InstitutionPicker: FunctionComponent<InstitutionPickerProps> = ({
  title,
  onValueChange,
  value,
  placeholder,
  disabled = false,
}) => {
  const { colors } = useTheme()
  const { data = [], isLoading } = useGetInstitutionTypesQuery()

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
            placeholder={placeholder ? placeholder : title}
            right={
              <TextInput.Icon
                icon={visible ? "chevron-up" : "chevron-down"}
                onPress={(event) => {
                  event.stopPropagation()
                  showDialog()
                }}
              />
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
          <RadioButton.Item
            key={`institutionType_${i.id}`}
            label={i.title}
            value={i.id.toString()}
          />
        ))}
      </RadioButton.Group>
    </Picker>
  )
}

export default InstitutionPicker
