import ProfileProvider from './ProfileProvider';
import { useNewOrganizationSearch } from '@/Apps/Addressbook/Composables'
import { asyncDebounce } from '@/Utils/Functions'

export default {
    name: 'NewOrganizationProvider',
    extends: ProfileProvider,
    service: asyncDebounce(useNewOrganizationSearch),
}