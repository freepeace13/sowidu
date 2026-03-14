/** @flow */

import { mapState, mapActions } from 'vuex';
import { StateService, CityService } from '~/services/MiscServices';
import { City, State } from '~/services/models/fundamentals';

export default () => ({
    data: () => ({
        cities: [],
        states: []
    }),

    computed: mapState({
        streets: (state) => state.misc.streets,
        countries: (state) => state.misc.countries,
        houseNumbers: (state) => state.misc.houseNumbers,
        zipcodes: (state) => state.misc.zipcodes,
        itemTypes: (state) => state.misc.itemTypes,
        units: (state) => state.misc.units,
        legalForms: (state) => state.misc.legalForms,
        institutionTypes: (state) => state.misc.institutionTypes,
        specializations: (state) => state.misc.specializations,
        permissions: (state) => state.misc.permissions
    }),

    methods: {
        ...mapActions({
            createCountry: 'misc/createCountry',
            createZipcode: 'misc/createZipcode',
            createStreet: 'misc/createStreet',
            createHouseNumber: 'misc/createHouseNumber'
        }),

        async getStates(countryId: number) {
            this.states = [];

            try {
                const results: Array<State> = await StateService.all(countryId);
                this.states = results;
                return this.states;
            } catch (error) {
                return Promise.reject(error);
            }
        },

        async getCities(countryId: number, stateId: number) {
            this.cities = [];

            try {
                const results: Array<City> = await CityService.all(countryId, stateId);
                this.cities = results;
                return this.cities;
            } catch (error) {
                return Promise.reject(error);
            }
        },

        async createCity(countryId: number, stateId: number, name: string) {
            try {
                const result: City = await CityService.create(countryId, stateId, name);
                this.cities.push(result);
                return result;
            } catch (error) {
                return Promise.reject(error);
            }
        },

        async createState(countryId: number, name: string) {
            try {
                const result: State = await StateService.create(countryId, name);
                this.states.push(result);
                return result;
            } catch (error) {
                return Promise.reject(error);
            }
        },
    }
})