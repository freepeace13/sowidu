<script>
import AutoComplete from './AutoComplete'
import TextField from '@/Apps/Shared/Components/AutoComplete/TextField.vue'
import JobTitleProvider from '../Providers/JobTitleProvider'

export default {
    name: 'JobTitleField',
    extends: AutoComplete,
    provider: JobTitleProvider,
    field: (h, props) => {
        return h(TextField, {
            on: {
                input: (e) => props.search(e),
            },
        })
    },
}
</script>
