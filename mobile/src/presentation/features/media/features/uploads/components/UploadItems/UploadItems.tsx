import { List } from "react-native-paper"

import { useUploadContext } from "../../hooks/useUploadContext"
import UploadProgress from "../UploadProgress/UploadProgress"

function UploadItems() {
  const { uploadingItems } = useUploadContext()
  return uploadingItems.length ? (
    <>
      <List.Subheader>Uploading</List.Subheader>
      {uploadingItems.map((item) => (
        <UploadProgress key={item.uuid} item={item} />
      ))}
    </>
  ) : null
}

export default UploadItems
