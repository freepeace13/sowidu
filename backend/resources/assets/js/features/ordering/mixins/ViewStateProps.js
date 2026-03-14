import { FILTER_TYPES } from '../enums';

export default () => ({
    props: {
        stateQuery: {
            type: String,
            validator(prop) {
                return Object.values(FILTER_TYPES.STATE).includes(prop);
            }
        }
    }
})