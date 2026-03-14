<script>
import { ProfileAutoComplete } from '../../Components/Fields'
import AutoComplete from '@/Apps/Shared/Containers/Fields/AutoComplete'

export default {
    name: 'ProfileField',

    extends: AutoComplete,

    provider: null,

    field: (h, props) => {
        return h(ProfileAutoComplete, {
            attrs: {
                noFilter: true,
                hideNoData: true,
            },
            on: {
                'update:searchInput': (e) => props.search(e, 5),
            },
        })
    },
}
</script>
