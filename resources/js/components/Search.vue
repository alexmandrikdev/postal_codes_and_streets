<template>
    <div>
        <b-form
            class="d-lg-flex justify-content-center align-items-start"
            @submit.prevent="search"
        >
            <b-form-group class="mx-2">
                <vue-typeahead-bootstrap
                    v-model="$v.postalCode.$model"
                    placeholder="Postal Code"
                    :input-class="
                        validateState('postalCode') === false
                            ? 'is-invalid'
                            : ''
                    "
                    :data="sortedPostalCodes"
                    :show-on-focus="true"
                    @keyup="debounceSearch"
                    @hit="search"
                />

                <b-form-invalid-feedback
                    :class="{
                        'd-block': validateState('postalCode') === false,
                    }"
                >
                    {{ postalCodeInvalidFeedback }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2">
                <vue-typeahead-bootstrap
                    v-model="$v.street.$model"
                    placeholder="Street"
                    :input-class="
                        validateState('street') === false ? 'is-invalid' : ''
                    "
                    :data="sortedStreets"
                    :show-on-focus="true"
                    @keyup="debounceSearch"
                    @hit="search"
                />

                <b-form-invalid-feedback
                    :class="{
                        'd-block': validateState('postalCode') === false,
                    }"
                >
                    The street must me at most 255 characters
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group class="mx-2 text-center">
                <b-button type="submit" variant="primary">Search</b-button>
            </b-form-group>
        </b-form>

        <b-table
            class="mt-2"
            :items="streets"
            :fields="['postal_code', 'name', 'latitude', 'longitude']"
            :per-page="perPage"
            :current-page="currentPage"
        />

        <b-pagination
            v-model="currentPage"
            :total-rows="streets.length"
            :per-page="perPage"
            aria-controls="my-table"
            align="center"
        />
    </div>
</template>

<script>
import axios from 'axios';
import { validationMixin } from 'vuelidate';
import { maxLength, numeric } from 'vuelidate/lib/validators';
import _debounce from 'lodash/debounce';
import VueTypeaheadBootstrap from 'vue-typeahead-bootstrap';

export default {
    components: {
        VueTypeaheadBootstrap,
    },
    mixins: [validationMixin],
    data() {
        return {
            postalCode: null,
            street: null,
            streets: [],
            perPage: 15,
            currentPage: 1,
        };
    },
    validations: {
        postalCode: {
            maxLength: maxLength(5),
            numeric,
        },
        street: {
            maxLength: maxLength(255),
        },
    },
    computed: {
        postalCodeInvalidFeedback() {
            return !this.$v.postalCode.numeric
                ? 'The postal code must be numeric'
                : !this.$v.postalCode.minLength || !this.$v.postalCode.maxLength
                ? 'The postal code must be 5 digit'
                : '';
        },
        sortedPostalCodes() {
            return this.streets.map(street => street.postal_code).sort();
        },
        sortedStreets() {
            return this.streets.map(street => street.name).sort();
        },
    },
    created() {
        this.search();
    },
    methods: {
        search() {
            this.$v.$touch();
            if (this.$v.$anyError) {
                return;
            }

            axios
                .get('/api/v1/streets', {
                    params: {
                        postal_code: this.postalCode,
                        name: this.street,
                    },
                })
                .then(response => {
                    this.streets = response.data.map(street => {
                        if (
                            (!street.latitude || !street.longitude) &&
                            street.postal_code2
                        ) {
                            street.longitude = street.postal_code2.longitude;
                            street.latitude = street.postal_code2.latitude;
                        }

                        return street;
                    });
                });
        },
        debounceSearch: _debounce(function() {
            this.search();
        }, 100),
        validateState(name) {
            const { $dirty, $error } = this.$v[name];
            return !$dirty ? null : $error ? false : null;
        },
    },
};
</script>

<style scoped></style>
