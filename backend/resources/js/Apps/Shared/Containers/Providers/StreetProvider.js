import AutoCompleteProvider from './AutoCompleteProvider'
import { useAutocompleteStreet } from '@/Apps/Shared/Composables/useAutocomplete'
import { asyncDebounce } from '@/Utils/Functions'

export default {
    name: 'StreetProvider',
    extends: AutoCompleteProvider,
    service: asyncDebounce(useAutocompleteStreet),
}