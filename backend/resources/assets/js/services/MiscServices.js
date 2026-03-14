/** @flow */

import { Collection } from '~/support/wrappers';
import {
    fetchLegalForms,
    fetchSpecializations,
    fetchInstitutionTypes
} from './api/company';

import {
    Country,
    State,
    City,
    Street,
    HouseNumber,
    Zipcode,
    InstitutionType,
    LegalForm,
    Specialization
} from './models/fundamentals';

import {
    fetchCountries,
    createCountry,
    fetchZipcodes,
    createZipcode,
    fetchHouseNumbers,
    createHouseNumber,
    fetchStreets,
    createStreet,
    fetchStates,
    createState,
    fetchCities,
    createCity
} from './api/address';

export const InstitutionTypeService = {
    async all(): Promise<Array<InstitutionType>> {
        const result: Array<Object> = await fetchInstitutionTypes();
        return result.map((v) => InstitutionType.create(v));
    }
}

export const LegalFormService = {
    async all(): Promise<Array<LegalForm>> {
        const result: Array<Object> = await fetchLegalForms();
        return result.map((v) => LegalForm.create(v));
    }
}

export const SpecializationService = {
    async all(): Promise<Array<Specialization>> {
        const result: Array<Object> = await fetchSpecializations();
        return result.map((v) => Specialization.create(v));
    }
}

export const CountryService = {
    async all(): Promise<Array<Country>> {
        const result: Array<Object> = await fetchCountries();
        return result.map((v) => Country.create(v));
    },

    async create(name: string): Promise<Country> {
        const result: Object = await createCountry(name);
        return Country.create(result);
    }
}

export const ZipcodeService = {
    async all(): Promise<Array<Zipcode>> {
        const result: Array<Object> = await fetchZipcodes();
        return result.map((v) => Zipcode.create(v));
    },

    async create(name: string): Promise<Zipcode> {
        const result: Object = await createZipcode(name);
        return Zipcode.create(result);
    }
}

export const HouseNumberService = {
    async all(): Promise<Array<HouseNumber>> {
        const result: Array<Object> = await fetchHouseNumbers();
        return result.map((v) => HouseNumber.create(v));
    },

    async create(name: string): Promise<HouseNumber> {
        const result: Object = await createHouseNumber(name);
        return HouseNumber.create(result);
    }
}

export const StreetService = {
    async all(): Promise<Array<Street>> {
        const result: Array<Object> = await fetchStreets();
        return result.map((v) => Street.create(v));
    },

    async create(name: string): Promise<Street> {
        const result: Object = await createStreet(name);
        return Street.create(result);
    }
}

export const StateService = {
    async all(countryId: number): Promise<Array<State>> {
        const result: Array<Object> = await fetchStates(countryId);
        return result.map((v) => State.create(v));
    },

    async create(countryId: number, name: string): Promise<State> {
        const result: Object = await createState(countryId, name);
        return State.create(result);
    }
}

export const CityService = {
    async all(countryId: number, stateId: number): Promise<Array<City>> {
        const result: Array<Object> = await fetchCities(countryId, stateId);
        return result.map((v) => City.create(v));
    },

    async create(countryId: number, stateId: number, name: string): Promise<City> {
        const result: Object = await createCity(countryId, stateId, name);
        return City.create(result);
    }
}

export default {
    CountryService,
    StateService,
    StreetService,
    HouseNumberService,
    CityService,
    ZipcodeService
}