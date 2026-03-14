import AutoCompleteProvider from './AutoCompleteProvider'
import { useAutocompleteZipcode } from '@/Apps/Shared/Composables/useAutocomplete'
import { asyncDebounce } from '@/Utils/Functions'

export default {
    name: 'ZipcodeProvider',
    extends: AutoCompleteProvider,
    service: asyncDebounce(useAutocompleteZipcode),
}