import { useGetCountries } from '../../Composables/usePlaceService'
import PlaceProvider from './PlaceProvider';

export default {
    name: 'CountryProvider',

    extends: PlaceProvider,

    created() {
        useGetCountries()
            .then(({ data }) => {
                this.items = Object.keys(data).map((key) => ({
                    value: key,
                    text: data[key],
                }))
            })
            .catch(console.error);
    },
}