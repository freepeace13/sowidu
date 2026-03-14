import { useGetCountryCities } from '../../Composables/usePlaceService'
import PlaceProvider from './PlaceProvider';

export default {
    name: 'CityProvider',

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

                useGetCountryCities(value)
                    .then((response) => {
                        this.items = Object.values(response.data);
                    })
                    .catch(console.error);
            },
        },
    },
}