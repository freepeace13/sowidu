<script>
import AutoCompleteField from './AutoComplete.vue'
import StreetProvider from '../../Providers/StreetProvider'

export default {
    name: 'StreetField',
    extends: AutoCompleteField,
    provider: StreetProvider,
}
</script>
