import ProfileService from '../Services/ProfileService'

export function useShowProfile(urn) {
    return new ProfileService().show(urn)
}
