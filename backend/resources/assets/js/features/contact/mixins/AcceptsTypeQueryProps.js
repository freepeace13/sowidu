import { FILTER_TYPES } from '../enums';

export default () => ({
    props: {
        typeQuery: {
            type: String,
            validator(prop) {
                return Object.values(FILTER_TYPES.RESOURCE).includes(prop);
            }
        }
    }
})