import { FunctionComponent, useRef, useState } from "react"
import { Dimensions, FlatList, ListRenderItem, View } from "react-native"
import { Card, Divider, IconButton, Menu, TextInput, useTheme } from "react-native-paper"
import ChatMessage, { ChatMessageProps } from "./ChatMessage"
import Animated, { useAnimatedStyle, useSharedValue } from "react-native-reanimated"
import { useGradualKeyboardAnimation } from "@presentation/hooks/useGradualKeyboardAnimation"
import { BottomSheetMethods } from "@gorhom/bottom-sheet/lib/typescript/types"
import BottomSheet, { BottomSheetScrollView, BottomSheetView } from "@gorhom/bottom-sheet"
import { type EmojiType, EmojiKeyboard } from "rn-emoji-keyboard"

import Style from "./ConversationStyle"

const VIEWPORT_HEIGHT = Dimensions.get("screen").height - 135
const SNAPPOINTS = ["40%", "50%", "60%"]
const EMOJI_SNAPPOINTS = ["43%"]
const MEDIA_LIBRARY_SNAPPOINTS = ["40%", "50%", "60%"]
const ATTACHMENT_SNAPPOINTS = ["18%"]

const assets = [
  { id: 1, uri: "https://i.pravatar.cc/300" },
  { id: 2, uri: "https://i.pravatar.cc/300" },
  { id: 3, uri: "https://i.pravatar.cc/300" },
  { id: 4, uri: "https://i.pravatar.cc/300" },
  { id: 5, uri: "https://i.pravatar.cc/300" },
  { id: 6, uri: "https://i.pravatar.cc/300" },
  { id: 7, uri: "https://i.pravatar.cc/300" },
  { id: 8, uri: "https://i.pravatar.cc/300" },
  { id: 9, uri: "https://i.pravatar.cc/300" },
  { id: 10, uri: "https://i.pravatar.cc/300" },
  { id: 11, uri: "https://i.pravatar.cc/300" },
  { id: 12, uri: "https://i.pravatar.cc/300" },
  { id: 13, uri: "https://i.pravatar.cc/300" },
  { id: 14, uri: "https://i.pravatar.cc/300" },
]

const calculateBottomSheetHeight = (toIndex: number, snapPoints: string[]) => {
  const multiplier = parseFloat((snapPoints[toIndex] || "0%").replace("%", ""))
  return Math.abs((multiplier / 100) * VIEWPORT_HEIGHT)
}

interface ConversationProps {
  messages: ChatMessageProps[]
}

const Conversation: FunctionComponent<ConversationProps> = ({ messages }) => {
  const { colors } = useTheme()
  const [inputFocused, setIsInputFocused] = useState(false)
  const [isOpen, setIsOpen] = useState(false)
  const [expanded, setExpanded] = useState(false)
  const [attachmentMenuVisible, setAttachmentMenuVisible] = useState(false)
  const [mediaLibraryVisible, setMediaLibraryVisible] = useState(false)
  const bottomSheetRef = useRef<BottomSheetMethods>(null)
  const bottomSheetEmojiRef = useRef<BottomSheetMethods>(null)
  const bottomSheetAttachmentMenuRef = useRef<BottomSheetMethods>(null)
  const bottomSheetHeight = useSharedValue(0)
  const { height } = useGradualKeyboardAnimation()
  const [newMessage, setNewMessage] = useState("")

  const fakeView = useAnimatedStyle(
    () => ({ height: Math.abs(height.value) || Math.abs(bottomSheetHeight.value) }),
    []
  )

  const handleMediaLibraryDismiss = () => {
    setMediaLibraryVisible(false)
  }

  const handleBottomSheetChanges = (toIndex: number) => {
    const multiplier = parseFloat((SNAPPOINTS[toIndex] || "0%").replace("%", ""))
    bottomSheetHeight.value = Math.abs((multiplier / 100) * VIEWPORT_HEIGHT)
  }

  const handleAttachmentMenuChanges = (toIndex: number) => {
    const multiplier = parseFloat((ATTACHMENT_SNAPPOINTS[toIndex] || "0%").replace("%", ""))
    bottomSheetHeight.value = Math.abs((multiplier / 100) * VIEWPORT_HEIGHT)
  }

  const handleEmojiDismiss = () => {
    setIsOpen(false)
  }

  const handleAttachmentMenuDismiss = () => {
    setAttachmentMenuVisible(false)
  }

  const RenderItem: ListRenderItem<ChatMessageProps> = ({ item, index }) => {
    return <ChatMessage key={index} {...item} />
  }

  const sendMessage = () => {
    console.log("send message", newMessage)
  }

  const sendTypingEvent = () => {
    //
  }

  const toggleEmojiPicker = () => {
    setIsOpen((prev) => !prev)
    bottomSheetEmojiRef.current?.snapToPosition(isOpen ? 0 : -1)
  }

  const toggleMediaLibrary = () => {
    setMediaLibraryVisible((prev) => !prev)
    bottomSheetRef.current?.snapToPosition(mediaLibraryVisible ? 0 : -1)
  }

  const toggleAttachmentMenu = () => {
    setAttachmentMenuVisible((prev) => !prev)
    // bottomSheetAttachmentMenuRef.current?.snapToPosition(attachmentMenuVisible ? 0 : -1)
  }

  const fetchOlderMessages = () => {
    console.log("fetch older messages")
  }

  const handlePick = (emojiObject: EmojiType) => {
    console.log(emojiObject)
    /* example emojiObject = {
        "emoji": "❤️",
        "name": "red heart",
        "slug": "red_heart",
        "unicode_version": "0.6",
      }
    */
  }

  const extraButtonsIsVisible = !inputFocused || (inputFocused && expanded)

  return (
    <>
      <FlatList
        showsVerticalScrollIndicator={false}
        inverted
        contentContainerStyle={Style.contentContainer}
        data={messages}
        initialNumToRender={15}
        renderItem={RenderItem}
        onEndReached={fetchOlderMessages}
        onEndReachedThreshold={0.1}
      />
      <View style={[Style.inputContainer]}>
        {/* <Avatar.Icon icon="account" size={40} style={Style.inputAvatar} /> */}
        {extraButtonsIsVisible && (
          <>
            <Menu
              visible={attachmentMenuVisible}
              onDismiss={handleAttachmentMenuDismiss}
              anchorPosition="top"
              statusBarHeight={60}
              mode="flat"
              elevation={2}
              contentStyle={{
                borderWidth: 1,
                borderColor: colors.outlineVariant,
              }}
              style={{
                // marginTop: -66,
                minWidth: 210,
                // borderWidth: 1,
                // borderColor: colors.outlineVariant,
              }}
              anchor={
                <IconButton
                  icon="plus-circle"
                  iconColor={colors.primary}
                  onPress={toggleAttachmentMenu}
                />
              }
            >
              <Menu.Item
                onPress={console.log}
                contentStyle={{ marginRight: "auto" }}
                trailingIcon="camera"
                title="Camera"
              />
              <Divider />
              <Menu.Item
                contentStyle={{ marginRight: "auto" }}
                onPress={console.log}
                trailingIcon="image"
                title="Photos"
              />
              <Divider />
              <Menu.Item
                contentStyle={{ marginRight: "auto" }}
                onPress={console.log}
                trailingIcon="map-marker"
                title="Location"
              />
            </Menu>
            <IconButton icon="camera" iconColor={colors.primary} />
            <IconButton icon="image" iconColor={colors.primary} />
          </>
        )}
        {inputFocused && (
          <IconButton
            icon={expanded ? "chevron-left" : "chevron-right"}
            iconColor={colors.primary}
            onPress={() => setExpanded((prev) => !prev)}
          />
        )}
        <TextInput
          value={newMessage}
          placeholder="Aa"
          mode="flat"
          underlineColor="transparent"
          activeUnderlineColor="transparent"
          onChangeText={setNewMessage}
          contentStyle={{
            flex: 1,
            marginHorizontal: 8,
          }}
          onFocus={() => {
            setIsInputFocused(true)
          }}
          onBlur={() => {
            setIsInputFocused(false)
          }}
          style={Style.input}
          right={
            <TextInput.Icon
              forceTextInputFocus={false}
              icon="emoticon-outline"
              color="#fa8231"
              onPress={toggleEmojiPicker}
            />
          }
        />
        <IconButton
          icon="send"
          disabled={!newMessage.length}
          iconColor={colors.primary}
          onPress={toggleMediaLibrary}
        />
      </View>
      <Divider />
      <Animated.View style={fakeView} />
      {/* <BottomSheet
        ref={bottomSheetAttachmentMenuRef}
        enablePanDownToClose
        enableContentPanningGesture={false}
        enableDynamicSizing={false}
        onChange={handleAttachmentMenuChanges}
        onClose={handleAttachmentMenuDismiss}
        index={attachmentMenuVisible ? 0 : -1}
        snapPoints={ATTACHMENT_SNAPPOINTS}
      >
        <BottomSheetView style={{ height: "100%" }}>
          <List.Item title="Photos" left={(props) => <List.Icon {...props} icon="image" />} />
          <List.Item title="Camera" left={(props) => <List.Icon {...props} icon="camera" />} />
        </BottomSheetView>
      </BottomSheet> */}
      <BottomSheet
        ref={bottomSheetEmojiRef}
        enablePanDownToClose
        enableContentPanningGesture={false}
        enableDynamicSizing={false}
        onChange={(toIndex: number) => {
          bottomSheetHeight.value = calculateBottomSheetHeight(toIndex, EMOJI_SNAPPOINTS)
        }}
        onClose={handleEmojiDismiss}
        index={isOpen ? 0 : -1}
        snapPoints={EMOJI_SNAPPOINTS}
      >
        <BottomSheetView style={{ height: 323 }}>
          <EmojiKeyboard
            onEmojiSelected={handlePick}
            categoryPosition="top"
            expandable={false}
            hideHeader={true}
            enableRecentlyUsed={false}
            enableSearchBar={false}
            enableSearchAnimation={false}
            styles={{
              container: {
                borderRadius: 0,
                shadowOpacity: 0,
              },
            }}
          />
        </BottomSheetView>
      </BottomSheet>
      <BottomSheet
        ref={bottomSheetRef}
        enablePanDownToClose
        enableContentPanningGesture
        enableDynamicSizing={false}
        onChange={(toIndex: number) => {
          bottomSheetHeight.value = calculateBottomSheetHeight(toIndex, MEDIA_LIBRARY_SNAPPOINTS)
        }}
        onClose={handleMediaLibraryDismiss}
        index={mediaLibraryVisible ? 0 : -1}
        snapPoints={MEDIA_LIBRARY_SNAPPOINTS}
      >
        <BottomSheetScrollView contentContainerStyle={{ flexDirection: "row", flexWrap: "wrap" }}>
          {assets.map((i) => (
            <Card key={i.id} style={{ width: 200 }}>
              <Card.Cover source={{ uri: i.uri }} />
            </Card>
          ))}
        </BottomSheetScrollView>
      </BottomSheet>
    </>
  )
}

export default Conversation
