import { FunctionComponent, createContext, useCallback, useContext, useMemo, useState } from "react"
import { updateMediaApiQueryData, useGetFilesQuery } from "../mediaApi"
import { FlatList, ImageURISource, ListRenderItem } from "react-native"
import { ActivityIndicator, List } from "react-native-paper"
import { readableBytes } from "@presentation/utils/readableBytes"
import { usePrivateURiSource } from "../hooks/usePrivateURiSource"
import FileCard from "../components/FileCard/FileCard"
import UploadItems from "../features/uploads/components/UploadItems/UploadItems"
import { View } from "react-native"
import { useAppDispatch } from "@presentation/app/store/hooks"
import { Media } from "@domain/media/media/Media"
import FileCardIcon from "../components/FileCard/FileCardIcon"

type InfiniteMediaFilesContextType = {
  prependItem: (entity: Media) => void
  updateItem: (entity: Media) => void
}

const InfiniteMediaFilesContext = createContext<InfiniteMediaFilesContextType>(
  {} as InfiniteMediaFilesContextType
)

export const useInfiniteMediaFiles = () => {
  const context = useContext(InfiniteMediaFilesContext)
  if (!context) {
    throw new Error("useInfiniteMediaFiles must be used within a InfiniteMediaFilesProvider")
  }
  return context
}

interface InfiniteMediaFilesProviderProps {
  onItemPress: (item: Media) => void
  isSelected: (item: Media) => boolean
  children: React.ReactNode
}

const InfiniteMediaFilesProvider: FunctionComponent<InfiniteMediaFilesProviderProps> = ({
  onItemPress,
  isSelected,
  children,
}) => {
  const dispatch = useAppDispatch()
  const privateURiCallback = usePrivateURiSource()
  const [page, setPage] = useState(1)
  const { data, isLoading, isFetching } = useGetFilesQuery({ page, limit: 10 })

  const items = useMemo(() => data?.list || [], [data])

  const keyExtractor = useCallback<(item: Media) => string>((item) => item.id, [])

  const updateItem = (entity: Media) => {
    dispatch(
      updateMediaApiQueryData("getFiles", { page }, (draft) => {
        const index = draft.list.findIndex((item) => item.id === entity.id)
        if (index !== -1) {
          draft.list.splice(index, 1, {
            ...draft.list[index],
            ...entity,
          })
        }
      })
    )
  }

  const prependItem = (entity: Media) => {
    dispatch(
      updateMediaApiQueryData("getFiles", { page: 1 }, (draft) => {
        Object.assign(draft, {
          list: [entity, ...draft.list],
        })
      })
    )
  }

  const loadMore = useCallback(() => {
    if (data && !isFetching && page !== data?.lastPage) {
      setPage((prevPage) => prevPage + 1)
    }
  }, [isFetching, data, page])

  const renderFooter = useCallback(() => {
    if (!isFetching) return null

    return (
      <View style={{ padding: 10 }}>
        <ActivityIndicator size="small" />
      </View>
    )
  }, [isFetching])

  const renderItem = useCallback<ListRenderItem<Media>>(
    ({ item }) => (
      <FileCard
        id={item.id}
        title={item.title}
        subtitle={readableBytes(item.file.size)}
        coverPhotoUri={privateURiCallback(item.file.thumbnail) as ImageURISource}
        highlight={isSelected(item)}
        showCoverPhoto
        shared={item.shared}
        onPress={() => onItemPress(item)}
        icon={(iconProps) => <FileCardIcon {...iconProps} type={item.file.type} />}
      />
    ),
    [isSelected, onItemPress, privateURiCallback]
  )

  return (
    <InfiniteMediaFilesContext.Provider value={{ updateItem, prependItem }}>
      <FlatList
        data={items}
        ListHeaderComponent={<ListHeader />}
        refreshing={isLoading}
        keyExtractor={keyExtractor}
        onEndReached={loadMore}
        onEndReachedThreshold={0.8}
        ListFooterComponent={renderFooter}
        renderItem={renderItem}
      />
      {children}
    </InfiniteMediaFilesContext.Provider>
  )
}

const ListHeader = () => (
  <>
    <UploadItems />
    <List.Subheader>Files</List.Subheader>
  </>
)

export default InfiniteMediaFilesProvider
