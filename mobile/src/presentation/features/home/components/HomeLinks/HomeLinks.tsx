import { useAccount } from "@presentation/features/account/hooks/useAccount"
import CardLink from "../CardLink/CardLink"
import { useAuthorization } from "@presentation/features/account/hooks/useAuthorization"

type HomeLinksPropsType = {
  onMediaPress: () => void
  onOrdersPress: () => void
  onAddressBookPress: () => void
  onChatsPress: () => void
  onTodoPress: () => void
  onDeliveryTicketsPress: () => void
}

function HomeLinks(props: HomeLinksPropsType) {
  const { user } = useAccount()
  const { hasPermissionTo } = useAuthorization(user)
  return (
    <>
      {hasPermissionTo("can access media") && (
        <CardLink
          title="Media Library"
          subtitle="300 file(s)"
          icon="folder-multiple-image"
          iconColor="#d32f2f"
          onPress={props.onMediaPress}
        />
      )}
      {hasPermissionTo("can access order") && (
        <CardLink
          title="Orders"
          subtitle="300 contact(s)"
          icon="cart"
          iconColor="#2196f3"
          onPress={props.onOrdersPress}
        />
      )}
      {hasPermissionTo("can access address book") && (
        <CardLink
          title="Addressbook"
          subtitle="300 contact(s)"
          icon="card-account-details"
          iconColor="#3949ab"
          onPress={props.onAddressBookPress}
        />
      )}
      {hasPermissionTo("can access chat") && (
        <CardLink
          title="Chats"
          subtitle="2 new message(s)"
          icon="chat"
          iconColor="#71269c"
          onPress={props.onChatsPress}
        />
      )}
      {hasPermissionTo("can access todo") && (
        <CardLink
          title="Todo"
          subtitle="2 board(s)"
          icon="clipboard-text"
          iconColor="#f2c24f"
          onPress={props.onTodoPress}
        />
      )}
      {hasPermissionTo("can access delivery tickets") && (
        <CardLink
          title="Deliveries"
          subtitle="30 ticket(s)"
          icon="truck"
          iconColor="#27ae60"
          onPress={props.onDeliveryTicketsPress}
        />
      )}
    </>
  )
}

export default HomeLinks
