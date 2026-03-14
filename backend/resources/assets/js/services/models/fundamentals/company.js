/** @flow */

import { GenericModel } from '~/support/wrappers';
import { ModelCollection as Collection } from '~/support/wrappers';

export class Specialization extends GenericModel {
    description: string;

    constructor(props: { description: string }): void {
        super(props);
        this.description = props.description;
    }

    static collection(collection: Array<Object>): Collection<Specialization> {
        return new Collection(collection.map((v) => Specialization.create(v)));
    }
}

export class InstitutionType extends GenericModel {
    static collection(collection: Array<Object>): Collection<InstitutionType> {
        return new Collection(collection.map((v) => InstitutionType.create(v)));
    }
}

export class LegalForm extends GenericModel {
    static collection(collection: Array<Object>): Collection<LegalForm> {
        return new Collection(collection.map((v) => LegalForm.create(v)));
    }
}