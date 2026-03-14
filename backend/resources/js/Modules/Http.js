import axios from 'axios';

/**
 * @deprecated this will be removed soon - Just use `axios` or `inertia`
 */
export default axios.create({
    timeout: 1000 * 10,
    headers: { 'X-Feature-Request': true },
});
