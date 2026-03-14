import { useGetCountryStates } from '../../Composables/usePlaceService'
import PlaceProvider from './PlaceProvider';

export default {
    name: 'StateProvider',

    extends: PlaceProvider,

    props: {
        country: {
            type: String,
            default: null,
        },
    },

    watch: {
        country: {
            immediate: true,
            handler(value) {
                if (!value) return;

                this.items = [];

                useGetCountryStates(value)
                    .then((response) => {
                        this.items = Object.values(response.data);
                    })
                    .catch(console.error);
            },
        },
    },
}