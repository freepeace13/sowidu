<script>
import AutoComplete from '../AutoComplete'
import AutoCompleteProvider from '../../Providers/AutoCompleteProvider'
import TextField from '@/Apps/Shared/Components/AutoComplete/TextField.vue'

export default {
    name: 'PlaceAutoCompleteField',
    extends: AutoComplete,
    provider: AutoCompleteProvider,
    field: (h, props) => {
        return h(TextField, {
            on: {
                input: (e) => props.search(e, 5),
            },
        })
    },
}
</script>
