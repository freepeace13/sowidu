import { FunctionComponent, useCallback } from "react"
import { AvatarPicker, AvatarPickerResult } from "@presentation/components/AvatarPicker"
import { useUpdateTeamAvatarMutation } from "../../teamsApi"
import { AvatarImageType } from "@domain/shared/types"
import { Team } from "@domain/teams/team/Team"

interface ProfileAvatarProps {
  team: Team
}

const ProfileAvatar: FunctionComponent<ProfileAvatarProps> = ({ team }) => {
  const [updateAvatar] = useUpdateTeamAvatarMutation()

  const handleCroppedImage = useCallback(
    (result: AvatarPickerResult) => {
      updateAvatar({
        teamId: team.id,
        image: {
          uri: result.uri,
          name: result.uri.split("/").pop() || "avatar.png",
          type: AvatarImageType.PNG,
        },
      })
    },
    [team, updateAvatar]
  )

  return <AvatarPicker uri={team.photoURL} onCropped={handleCroppedImage} />
}

export default ProfileAvatar
