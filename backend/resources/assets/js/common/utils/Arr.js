
export default class Arr {
    static query(key, values) {
        return values.map((v) => `${key}[]=${v}`).join('&');
    }
}