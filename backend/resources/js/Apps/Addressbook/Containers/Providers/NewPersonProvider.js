import ProfileProvider from './ProfileProvider';
import { useNewPersonSearch } from '@/Apps/Addressbook/Composables'
import { asyncDebounce } from '@/Utils/Functions'

export default {
    name: 'NewPersonProvider',
    extends: ProfileProvider,
    service: asyncDebounce(useNewPersonSearch),
}
