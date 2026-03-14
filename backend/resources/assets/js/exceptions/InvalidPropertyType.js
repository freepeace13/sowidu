import { parseConstructorNames, arrwrap } from '~/support/helpers';

export default class InvalidPropertyType extends Error {
    constructor(message) {
        super(message);
    }

    static rules(rules, property, value) {
        const ruleNames = parseConstructorNames(...arrwrap(rules));

        return new InvalidPropertyType(`
            The property "${property}" value of "${value}" is invalid [${ruleNames}] rules.
        `);
    }
}