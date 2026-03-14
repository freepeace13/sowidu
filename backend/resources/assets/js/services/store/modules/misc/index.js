/** @flow */

import * as types from './constants';
import ItemService from '~/services/ItemService';

import {
    CountryService,
    ZipcodeService,
    StreetService,
    HouseNumberService,
    InstitutionTypeService,
    LegalFormService,
    SpecializationService
} from '~/services/MiscServices';

import {
    Country,
    Zipcode,
    Street,
    HouseNumber,
    InstitutionType,
    LegalForm,
    Specialization,
    Unit,
    Type,
    Permission
} from '~/services/models/fundamentals';

export default {
    namespaced: true,

    state: {
        streets: [],
        countries: [],
        houseNumbers: [],
        zipcodes: [],
        itemTypes: [],
        units: [],
        legalForms: [],
        institutionTypes: [],
        specializations: [],
        permissions: []
    },

    actions: {
        async fetchLegalForms({ commit }: Object): Promise<void> {
            const result: Array<LegalForm> = await LegalFormService.all();
            commit(types.SET_LEGAL_FORMS, result);
        },

        async fetchInsitutionTypes ({ commit }: Object): Promise<void> {
            const result: Array<InstitutionType> = await InstitutionTypeService.all();
            commit(types.SET_INSTITUTION_TYPES, result);
        },

        async fetchSpecializations ({ commit }: Object): Promise<void> {
            const result: Array<Specialization> = await SpecializationService.all();
            commit(types.SET_SPECIALIZATIONS, result);
        },

        async fetchUnits({ commit }: Object): Promise<void> {
            const result: Array<Unit> = await ItemService.fetchUnits();
            commit(types.SET_UNITS, result);
        },

        async fetchTypes({ commit }: Object): Promise<void> {
            const result: Array<Type> = await ItemService.fetchTypes();
            commit(types.SET_TYPES, result);
        },

        async fetchCountries({ commit }: Object): Promise<void> {
            const result: Array<Country> = await CountryService.all();
            commit(types.SET_COUNTRIES, result);
        },

        async createCountry({ commit }: Object, name: string): Promise<void> {
            const result: Country = await CountryService.create(name);
            commit(types.INSERT_COUNTRY, result);
        },

        async fetchZipcodes({ commit }: Object): Promise<void> {
            const result: Array<Zipcode> = await ZipcodeService.all();
            commit(types.SET_ZIPCODES, result);
        },

        async createZipcode({ commit }: Object, name: string): Promise<void> {
            const result: Zipcode = await ZipcodeService.create(name);
            commit(types.INSERT_ZIPCODE, result);
        },

        async fetchStreets({ commit }: Object): Promise<void> {
            const result: Array<Street> = await StreetService.all();
            commit(types.SET_STREETS, result);
        },

        async createStreet({ commit }: Object, name: string): Promise<void> {
            const result: Street = await StreetService.create(name);
            commit(types.INSERT_STREET, result);
        },

        async fetchHouseNumbers({ commit }: Object): Promise<void> {
            const result: Array<HouseNumber> = await HouseNumberService.all();
            commit(types.SET_HOUSE_NUMBERS, result);
        },

        async createHouseNumber({ commit }: Object, name: string): Promise<void> {
            const result: HouseNumber = await HouseNumberService.create(name);
            commit(types.INSERT_HOUSE_NUMBER, result);
        },
    },

    mutations: {
        [types.SET_LEGAL_FORMS] (state: Object, legalforms: Array<LegalForm>) {
            state.legalForms = legalforms;
        },

        [types.SET_INSTITUTION_TYPES] (state: Object, types: Array<InstitutionType>) {
            state.institutionTypes = types;
        },

        [types.SET_SPECIALIZATIONS] (state: Object, specializations: Array<Specialization>) {
            state.specializations = specializations;
        },

        [types.SET_UNITS] (state: Object, units: Array<Unit>) {
            state.units = units;
        },

        [types.SET_TYPES] (state: Object, types: Array<ItemType>) {
            state.itemTypes = types;
        },

        [types.SET_COUNTRIES] (state: Object, countries: Array<Country>): void {
            state.countries = countries;
        },

        [types.INSERT_COUNTRY] (state: Object, country: Country): void {
            state.countries = Country
                .collection(state.countries)
                .insert(country)
                .all();
        },

        [types.SET_ZIPCODES] (state: Object, zipcodes: Array<Zipcode>): void {
            state.zipcodes = zipcodes;
        },

        [types.INSERT_ZIPCODE] (state: Object, zipcode: Zipcode): void {
            state.zipcodes = Zipcode
                .collection(state.zipcodes)
                .insert(zipcode)
                .all();
        },

        [types.SET_STREETS] (state: Object, streets: Array<Street>): void {
            state.streets = streets;
        },

        [types.INSERT_STREET] (state: Object, street: Street): void {
            state.streets = Street
                .collection(state.streets)
                .insert(street)
                .all();
        },

        [types.SET_HOUSE_NUMBERS] (state: Object, houseNumbers: Array<HouseNumber>): void {
            state.houseNumbers = houseNumbers;
        },

        [types.INSERT_HOUSE_NUMBER] (state: Object, houseNumber: HouseNumber): void {
            state.houseNumbers = HouseNumber
                .collection(state.houseNumbers)
                .insert(houseNumber)
                .all();
        },

        [types.SET_PERMISSIONS] (state, payload): void {
            state.permissions = payload.map((v) => Permission.create(v));
        }
    }
}