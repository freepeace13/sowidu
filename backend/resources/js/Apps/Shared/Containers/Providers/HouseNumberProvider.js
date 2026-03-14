import AutoCompleteProvider from './AutoCompleteProvider'
import { useAutocompleteHouseNumber } from '@/Apps/Shared/Composables/useAutocomplete'
import { asyncDebounce } from '@/Utils/Functions'

export default {
    name: 'HouseNumberProvider',
    extends: AutoCompleteProvider,
    service: asyncDebounce(useAutocompleteHouseNumber),
}