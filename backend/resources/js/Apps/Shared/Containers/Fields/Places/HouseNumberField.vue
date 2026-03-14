<script>
import AutoCompleteField from './AutoComplete.vue'
import HouseNumberProvider from '../../Providers/HouseNumberProvider'

export default {
    name: 'HouseNumberField',
    extends: AutoCompleteField,
    provider: HouseNumberProvider,
}
</script>
