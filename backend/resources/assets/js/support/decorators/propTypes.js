import * as helpers from '../helpers';

const argsValue = (args) => helpers.isFunction(args) ? args.call() : args;

export const Primitives = (...types) => function Primitive(value) {
    return types.some(i => i.name === helpers.toRawType(value));
}

export const Nullable = () => function Nullable(value) {
    return value === null || value === undefined;
}

export const Email = () => function isEmail(value) {
    return helpers.isEmail(value);
}

export const Resource = (args) => function Resource(value) {
    return value instanceof argsValue(args);
}

export const NullableString = () => function NullableString(value) {
    return Nullable()(value) || Primitives(String)(value);
}

export const NullableNumber = () => function NullableNumber(value) {
    return Nullable()(value) || Primitives(Number)(value);
}

export const NullableEmail = () => function NullableEmail(value) {
    return Nullable()(value) || Email()(value);
}

export const OneOf = (args) => function OneOf(value) {
    return helpers.isOneOf(argsValue(args), value);
}

export const SetOf = (args) => function SetOf(value) {
    let values = argsValue(args);

    return Array.isArray(value) && value.every(
        i => i === values ||
        i instanceof values ||
        helpers.toRawType(i) === helpers.toRawType(values)
    );
}

export const SetOfSome = (args) => function SetOfSome(value) {
    let values = helpers.arrwrap(argsValue(args));

    return Array.isArray(value) && value.every(
        i => helpers.isOneOf(values, i)
    );
}