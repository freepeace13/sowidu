import React, { useContext } from "react"
import { View } from "react-native"
import { Button, Icon, IconButton, useTheme } from "react-native-paper"

import Style from "./style"
import { FilesContext, ListType } from "../Context"

interface Props {
  listType: ListType
  onListTypeChange: (value: ListType) => void
}

function FilterBar({ listType, onListTypeChange }: Props) {
  const { colors } = useTheme()

  const toggleListType = () => {
    onListTypeChange(listType === ListType.Grid ? ListType.List : ListType.Grid)
  }

  const renderListTypeIcon = () => {
    return listType === ListType.Grid ? "format-list-bulleted" : "view-grid"
  }

  return (
    <View style={Style.container}>
      <Button
        mode="text"
        textColor={colors.onSurface}
        contentStyle={{ flexDirection: "row-reverse" }}
        icon={(props) => <Icon {...props} source="filter-variant" size={20} />}
      >
        Filter
      </Button>
      <IconButton
        size={20}
        icon={renderListTypeIcon()}
        iconColor={colors.onSurface}
        onPress={toggleListType}
      />
    </View>
  )
}

export default function FilterBarContainer() {
  const { listType, setListType } = useContext(FilesContext)
  return <FilterBar listType={listType} onListTypeChange={setListType} />
}
