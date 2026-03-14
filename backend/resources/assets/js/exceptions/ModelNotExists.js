/** @flow */

export default class ModelNotExists extends Error {
    constructor(message: string): void {
        super(message);
    }

    static fromRaw(alias: string, choices: Array<string>): ModelNotExists {
        return new ModelNotExists(
            `Model alias [${alias}] not exists from choices [${choices.join(', ')}]`
        );
    }
}