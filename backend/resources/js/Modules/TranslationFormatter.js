
export default class TranslationFormatter {
    interpolate(message, values) {
        if (values && typeof values === 'object') {
            Object.keys(values).forEach((key) => message = message.replace(`:${key}`, values[key]));
        }
        
        return [message];
    }
}