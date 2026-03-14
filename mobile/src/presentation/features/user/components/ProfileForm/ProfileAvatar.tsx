import { FunctionComponent } from "react"
import { AvatarPicker, AvatarPickerResult } from "@presentation/components/AvatarPicker"
import { useUpdateUserAvatarMutation } from "@presentation/features/user/userApi"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { isErrorWithMessage } from "@application/services/helpers"

interface ProfileAvatarProps {}

const ProfileAvatar: FunctionComponent<ProfileAvatarProps> = () => {
  const { user } = useAccount()
  const flashMessage = useFlashMessage()
  const [updateAvatar] = useUpdateUserAvatarMutation()

  const handleCroppedImage = async (result: AvatarPickerResult) => {
    try {
      await updateAvatar({
        avatar: {
          uri: result.uri,
          name: result.uri.split("/").pop() || "avatar.png",
          type: "image/png",
        },
      }).unwrap()

      flashMessage.showMessage("Saved")
    } catch (e) {
      if (isErrorWithMessage(e)) {
        flashMessage.showMessage(e.message)
      }
    }
  }

  if (!user) {
    return null
  }

  return <AvatarPicker uri={user?.photoURL} onCropped={handleCroppedImage} />
}

export default ProfileAvatar
