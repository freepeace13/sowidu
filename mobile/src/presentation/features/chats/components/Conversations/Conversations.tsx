import { Conversation as ConversationDomain } from "@domain/chats/Conversation"
import { FunctionComponent, useRef, useState } from "react"
import { FlatList, ListRenderItem } from "react-native"
import { Divider, List, Searchbar } from "react-native-paper"
import { BottomSheetModal } from "@gorhom/bottom-sheet"

import { useGetConversationsQuery } from "../../chatsApi"
import Conversation from "./Conversation"
import { User } from "@domain/user/User"
import BottomSheetMenu from "@presentation/components/BottomSheetMenu/BottomSheetMenu"
import BottomSheetMenuItem from "@presentation/components/BottomSheetMenu/BottomSheetMenuItem"

interface ConversationsProps {
  user: User
  onPress: (item: ConversationDomain) => void
}

const Conversations: FunctionComponent<ConversationsProps> = ({ user, onPress }) => {
  const [searchText, setSearchText] = useState("")
  const selectedRef = useRef<ConversationDomain | null>(null)
  const bottomSheetRef = useRef<BottomSheetModal>(null)
  const { data, isLoading } = useGetConversationsQuery({ page: 1 })

  const RenderItem: ListRenderItem<ConversationDomain> = ({ item }) => (
    <Conversation
      key={item.id.toString()}
      model={item}
      user={user}
      onLongPress={() => handleLongPress(item)}
      onPress={() => onPress(item)}
      unread={true}
    />
  )

  const handleLongPress = (conversation: ConversationDomain) => {
    selectedRef.current = conversation
    bottomSheetRef.current?.present()
  }

  return (
    <>
      <Searchbar
        placeholder="Search"
        value={searchText}
        onChangeText={setSearchText}
        style={{ marginHorizontal: 14, marginTop: 10 }}
      />
      <List.Subheader>Messages</List.Subheader>
      <FlatList
        data={data?.list || []}
        keyExtractor={(item) => item.id.toString()}
        refreshing={isLoading}
        initialNumToRender={15}
        renderItem={RenderItem}
        showsVerticalScrollIndicator={false}
      />
      <BottomSheetMenu ref={bottomSheetRef}>
        <BottomSheetMenuItem title="Open" disabled={false} icon="eye" onPress={console.log} />
        <BottomSheetMenuItem title="Rename" disabled={false} icon="pencil" onPress={console.log} />
        <BottomSheetMenuItem
          title="Add Member"
          disabled={false}
          icon="account-multiple-plus"
          onPress={console.log}
        />
        <Divider />
        <BottomSheetMenuItem
          title="Delete Conversation"
          disabled={false}
          icon="delete"
          onPress={console.log}
        />
      </BottomSheetMenu>
      {/* <BottomSheet
        ref={bottomSheetRef}
        enablePanDownToClose
        enableContentPanningGesture
        enableDynamicSizing={false}
        index={-1}
        snapPoints={["25%"]}
      >
        <BottomSheetScrollView contentContainerStyle={{ flexDirection: "row", flexWrap: "wrap" }}>
          <List.Item title="Rename" />
          <List.Item title="Add Member" />
          <List.Item title="Delete Conversation" />
        </BottomSheetScrollView>
      </BottomSheet> */}
    </>
  )
}

export default Conversations
