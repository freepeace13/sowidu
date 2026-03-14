<script>
import AutoCompleteField from './AutoComplete.vue'
import ZipcodeProvider from '../../Providers/ZipcodeProvider'

export default {
    name: 'ZipcodeField',
    extends: AutoCompleteField,
    provider: ZipcodeProvider,
}
</script>
