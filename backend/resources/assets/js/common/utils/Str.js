
export default class Str {
    static random(length = null) {
        const result = Math.random().toString(36);
        return !length ? result : result.substring(length);
    }
}