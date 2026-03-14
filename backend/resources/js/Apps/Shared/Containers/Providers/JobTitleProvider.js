import AutoCompleteProvider from './AutoCompleteProvider'
import { useAutocompleteJobTitle } from '@/Apps/Shared/Composables/useAutocomplete'
import { asyncDebounce } from '@/Utils/Functions'

export default {
    name: 'JobTitleProvider',
    extends: AutoCompleteProvider,
    service: asyncDebounce(useAutocompleteJobTitle),
}