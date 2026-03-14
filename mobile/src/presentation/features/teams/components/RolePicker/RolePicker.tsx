import Picker from "@presentation/components/Picker/Picker"
import { Pressable, View } from "react-native"
import { Checkbox, ProgressBar, RadioButton, TextInput, useTheme } from "react-native-paper"

import Style from "./RolePickerStyle"
import { FunctionComponent, useMemo, useState } from "react"
import { useGetRolesQuery } from "@presentation/features/teams/teamsApi"
import { Role } from "@domain/teams/permissions/Role"

interface BaseRolePickerProps {
  title: string
  value: string[] | string | undefined
  teamId: number
  label?: string
  // multiple?: boolean
  placeholder?: string
  valueExtractor?: (item: Role) => string
  filter?: (item: Role) => boolean
  // onValueChange: (value: string | string[]) => void
  left?: React.ReactNode
  readonly?: boolean
  disabled?: boolean
}

interface SingleRolePickerProps extends BaseRolePickerProps {
  multiple?: false
  onValueChange: (value: string) => void
}

interface MultiRolePickerProps extends BaseRolePickerProps {
  multiple: true
  onValueChange: (value: string[]) => void
}

type RolePickerProps = SingleRolePickerProps | MultiRolePickerProps

const parseSelectedValue = (
  value: RolePickerProps["value"],
  props: Pick<RolePickerProps, "multiple">
) => {
  if (props.multiple) {
    return Array.isArray(value) ? value : []
  } else if (typeof value === "string") {
    return [value]
  }
  return []
}

const RolePicker: FunctionComponent<RolePickerProps> = ({
  title,
  label,
  onValueChange,
  value,
  placeholder,
  teamId,
  readonly = false,
  multiple = false,
  disabled = false,
  filter,
  ...restProps
}) => {
  const { colors } = useTheme()
  const { items, isLoading } = useGetSelectionItems({ filter, teamId })
  const initialValue = parseSelectedValue(value, { multiple })
  const [selected, setSelected] = useState<string[]>(initialValue)

  const isValid = (value: any) => {
    return items.findIndex((i) => getItemIdentifier(restProps, i) === value) !== -1
  }

  const getStatus = (value: Role) => {
    const itemId = getItemIdentifier(restProps, value)
    return selected.includes(itemId) ? "checked" : "unchecked"
  }

  const handleValueChange = (newVal: string) => {
    if (!isValid(newVal)) {
      return
    }

    setSelected((prev) => {
      if (multiple) {
        if (prev.includes(newVal)) {
          return prev.filter((i) => i !== newVal)
        }
        return [...prev, newVal]
      }

      return [newVal]
    })
  }

  const displayValue = useMemo(
    () => selected.map((i) => items.find(({ id }) => +i === id)?.name || i).join(","),
    [selected, items]
  )

  return (
    <Picker
      title={title}
      onDismissed={() => {
        if (multiple) {
          ;(onValueChange as MultiRolePickerProps["onValueChange"])(selected)
        } else {
          ;(onValueChange as SingleRolePickerProps["onValueChange"])(selected.join(""))
        }
      }}
      anchor={({ showDialog, visible }) => (
        <Pressable onPress={() => !readonly && showDialog()}>
          <TextInput
            {...restProps}
            label={label}
            mode="outlined"
            editable={false}
            disabled={isLoading || disabled}
            placeholder={placeholder}
            value={displayValue}
            right={
              <TextInput.Icon
                icon={visible ? "chevron-up" : "chevron-down"}
                disabled={disabled || readonly}
                color={colors.secondary}
                onPress={(event) => {
                  event.stopPropagation()
                  if (!readonly) {
                    showDialog()
                  }
                }}
              />
            }
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
      {multiple ? (
        <View>
          {items.map((i) => (
            <Checkbox.Item
              key={`check_role_${i.id}`}
              label={i.name}
              status={getStatus(i)}
              onPress={() => handleValueChange(getItemIdentifier(restProps, i))}
            />
          ))}
        </View>
      ) : (
        <RadioButton.Group onValueChange={handleValueChange} value={selected.join("")}>
          {items.map((i) => (
            <RadioButton.Item
              key={`radio_roles_${i.id}`}
              label={i.name}
              value={getItemIdentifier(restProps, i)}
            />
          ))}
        </RadioButton.Group>
      )}
    </Picker>
  )
}

const getItemIdentifier = (props: Pick<RolePickerProps, "valueExtractor">, item: Role) => {
  return typeof props.valueExtractor === "function"
    ? props.valueExtractor(item)
    : item.id.toString()
}

const useGetSelectionItems = (props: Pick<RolePickerProps, "filter" | "teamId">) => {
  const { data = [], isLoading } = useGetRolesQuery({ teamId: props.teamId })
  const items = useMemo(
    () => data.filter((i) => (typeof props.filter === "function" ? props.filter(i) : true)),
    [props, data]
  )
  return { items, isLoading }
}

export default RolePicker
